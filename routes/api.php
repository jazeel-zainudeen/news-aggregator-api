<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\UserPreferenceController;
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
    Route::controller(LookupController::class)->prefix('lookup')->group(function () {
        Route::get('categories', 'listCategories')->name('lookup.categories');
        Route::get('sources', 'listSources')->name('lookup.sources');
        Route::get('authors', 'listAuthors')->name('lookup.authors');
    });

    Route::controller(ArticleController::class)->group(function () {
        Route::get('articles', 'index')->name('articles.index');
        Route::get('articles/personalized', 'personalizedFeed')->name('articles.personalized');
        Route::get('articles/{article}', 'show')->name('articles.show');
    });

    Route::controller(UserPreferenceController::class)->prefix('user/preferences')->group(function () {
        Route::put('/', 'update')->name('user.preferences.update');
        Route::get('/', 'index')->name('user.preferences.index');
    });
});
