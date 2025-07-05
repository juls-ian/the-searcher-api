<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Middleware\HandleExpiredTokens;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

/**
 * User Auth routes
 */
Route::prefix('auth')->group(function () {

    // Login 
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login');
    // Password recovery 
    Route::post('/forgot-password', ForgotPasswordController::class)->middleware('throttle:5,1');
    Route::post('/reset-password', ResetPasswordController::class)->middleware('throttle:5,1');

    // Set Password
    Route::post('/set-password', SetPasswordController::class)->middleware('throttle:5,1');


    Route::middleware(['auth:sanctum', HandleExpiredTokens::class])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/current-user', [AuthController::class, 'currentUser']);

    });

});

/**
 * Article routes 
 */
Route::apiResource('articles', ArticleController::class)->middleware('auth:sanctum'); # change to auth:sanctum later

/**
 * User routes
 */
Route::apiResource('users', UserController::class)->middleware('guest'); # change to auth:sanctum later

/**
 * Article Category routes 
 */
Route::apiResource('article-categories', ArticleCategoryController::class)->middleware('guest'); # change to auth:sanctum later


/**
 * Email verification routes
 */
Route::prefix('email')->middleware('auth:sanctum')->group(function () {

    // Email verification notice 
    Route::post('/verify', [EmailVerificationController::class, 'send'])
        ->middleware(['throttle:5,1'])
        ->name('verification.notice');

    // Email verification handler
    Route::get('/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resending verification email 
    Route::post('/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

});