<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\AuthResource;
use App\Mail\ForgetPasswordEmail;
use App\Mail\WelcomeEmail;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\AuthRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Authentication API Documentation",
 *     description="API endpoints for user authentication and password management",
 *
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 */
class AuthController extends Controller
{
    public function __construct(public AuthRepository $authRepository) {}

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(

     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="token", type="string", format="3|RkOefsYDFPCOaoYSwlsZ77b4q85W1Qv34y5lbhWobb2643b0")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = $this->authRepository->store($request->validated());
        Mail::to($user)->queue(new WelcomeEmail($user));

        return response()->json(AuthResource::make($user));
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Authenticate user and create session",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="token", type="string", format="3|RkOefsYDFPCOaoYSwlsZ77b4q85W1Qv34y5lbhWobb2643b0")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $res = auth()->guard('web')->attempt($credentials);
        if (! $res) {
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        return response()->json(AuthResource::make(auth()->user()));
    }

    /**
     * @OA\Post(
     *     path="/auth/forget-password",
     *     summary="Send password reset link to email",
     *     tags={"Password Reset"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->authRepository->forgetPassword($data['email']);

        if (! empty($token)) {
            $user = User::where('email', $data['email'])->first();
            Mail::to($user)->queue(new ForgetPasswordEmail($token));
        }

        return response()->json(['message' => trans('auth.forget_password')]);
    }

    public function showResetPasswordForm($token): View|Factory|Application
    {
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }

    public function submitResetPasswordForm(ResetPasswordRequest $request): Application|Redirector|RedirectResponse
    {
        try {
            $request->validated();
            $passwordReset = PasswordReset::where(['email' => $request->email])->first();
            if (empty($passwordReset) || ! Hash::check($request->token, $passwordReset->token)) {
                return back()->withInput()->with('error', trans('auth.invalid_token'));
            }
            User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
            PasswordReset::where(['email' => $request->email])->delete();

            return back()->withInput()->with('success', trans('auth.password_reset_success'));
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
