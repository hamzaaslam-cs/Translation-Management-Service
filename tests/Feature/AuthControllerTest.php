<?php

use App\Mail\ForgetPasswordEmail;
use App\Mail\WelcomeEmail;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

// Fake mailing before running tests
beforeEach(function () {
    Mail::fake();
});

it('registers a new user and sends a welcome email', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson(route('auth.register'), $userData);

    $response->assertStatus(200)
        ->assertJsonStructure(['name', 'email', 'token']);

    Mail::assertQueued(WelcomeEmail::class);
    expect(User::where('email', 'john@example.com')->exists())->toBeTrue();
});

it('fails login with incorrect credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('correct_password')]);

    $response = $this->postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrong_password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

it('logs in a user with correct credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['name', 'email', 'token']);

    expect(Auth::check())->toBeTrue();
});

it('sends a password reset email if email exists', function () {
    $user = User::factory()->create();

    $response = $this->postJson(route('auth.forget-password'), [
        'email' => $user->email,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => trans('auth.forget_password')]);

    Mail::assertQueued(ForgetPasswordEmail::class);
});

it('shows the reset password form', function () {
    $token = 'test-token';

    $response = $this->get(route('auth.password-reset-form', ['token' => $token]));

    $response->assertStatus(200)
        ->assertViewIs('auth.forgetPasswordLink')
        ->assertViewHas('token', $token);
});

it('resets password with valid token', function () {
    $user = User::factory()->create(['email' => 'john@example.com']);
    $token = Hash::make('reset-token');

    PasswordReset::create([
        'email' => 'john@example.com',
        'token' => $token,
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->post(route('reset.password.post'), [
        'email' => 'john@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'token' => 'reset-token',
    ]);

    $response->assertRedirect();
    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});
