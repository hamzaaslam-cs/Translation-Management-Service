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

class AuthController extends Controller
{
    public function __construct(public AuthRepository $authRepository)
    {
    }

    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = $this->authRepository->store($request->validated());
        Mail::to($user)->queue(new WelcomeEmail($user));

        return response()->json(AuthResource::make($user));

    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $res = auth()->guard('web')->attempt($credentials);
        if (!$res) {
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        return response()->json(AuthResource::make(auth()->user()));
    }

    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->authRepository->forgetPassword($data['email']);
        if (!empty($token)) {
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
            if (empty($passwordReset) || !Hash::check($request->token, $passwordReset->token)) {
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
