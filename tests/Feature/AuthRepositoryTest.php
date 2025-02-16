<?php

use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can store a user', function () {
    $repository = new AuthRepository;

    $user = $repository->store([
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password',
    ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->email)->toBe('johndoe@example.com');
});

test('it can update a user', function () {
    $user = User::factory()->create();
    $repository = new AuthRepository;

    $repository->update($user->id, ['name' => 'Updated Name']);

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

test('it generate a password reset token', function () {
    PasswordReset::factory()->create(['email' => 'test@example.com', 'expires_at' => now()->subMinutes(5)]);

    $repository = new AuthRepository;

    $token = $repository->forgetPassword('test@example.com');

    expect($token)->toBeNull()
        ->and(PasswordReset::where('email', 'test@example.com')->exists())->toBeTrue();
});

test('it does not generate a token if it is not expired', function () {
    PasswordReset::factory()->create(['email' => 'test@example.com', 'expires_at' => now()->addMinutes(5)]);
    $repository = new AuthRepository;

    $token = $repository->forgetPassword('test@example.com');

    expect($token)->not->toBeNull();
});

test('it does not generate a token if invalid email is provided', function () {
    PasswordReset::factory()->create(['email' => 'test@example.com', 'expires_at' => now()->addMinutes(5)]);
    $repository = new AuthRepository;

    $token = $repository->forgetPassword('test1@example.com');

    expect($token)->not->toBeNull();
});
