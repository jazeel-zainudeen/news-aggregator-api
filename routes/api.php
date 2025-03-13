<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticatedSessionController::class)->group(function () {
    Route::post('login', 'store')->name('auth.login');
    Route::delete('logout', 'destroy')->middleware('auth:sanctum')->name('auth.logout');
});

Route::controller(RegisteredUserController::class)->group(function () {
    Route::post('register', 'store')->name('auth.register');
});

Route::controller(PasswordResetController::class)->group(function () {
    Route::post('password/forgot', 'sendResetLink')->name('password.forgot');
    Route::post('password/reset', 'reset')->name('password.reset');
});
