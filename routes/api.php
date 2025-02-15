<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;


Route::prefix('/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forget', [AuthController::class, 'forgetPassword']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('translations/export', [TranslationController::class, 'exportJson']);
    Route::apiResource('translations', TranslationController::class);

});
