<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('auth.password-reset-form');
Route::post('reset-password', [AuthController::class, 'submitResetPasswordForm'])->name('reset.password.post');
