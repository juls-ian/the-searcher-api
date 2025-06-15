<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Middleware\HandleExpiredTokens;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// User Auth routes
Route::prefix('auth')->group(function () {

    // Login 
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    // Password recovery 
    Route::post('/forgot-password', ForgotPasswordController::class)->middleware('throttle:5,1');
    Route::post('/reset-password', ResetPasswordController::class)->middleware('throttle:5,1');


    Route::middleware(['auth:sanctum', HandleExpiredTokens::class])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/current-user', [AuthController::class, 'currentUser']);

    });

});

// Article routes 
Route::controller(ArticleController::class)->group(function () {
    Route::apiResource('articles', ArticleController::class);
})->middleware('guest');

// User routes
Route::controller(UserController::class)->group(function () {
    Route::apiResource('users', UserController::class);
});