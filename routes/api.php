<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FilterController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(FilterController::class)->prefix('filters')->group(function () {
        Route::get('categories', 'listCategories')->name('filter.categories');
        Route::get('sources', 'listSources')->name('filter.sources');
    });

    Route::controller(ArticleController::class)->group(function () {
        Route::get('articles', 'index')->name('articles.index');
    });
});
