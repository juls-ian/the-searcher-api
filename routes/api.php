<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// User Auth routes
Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
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