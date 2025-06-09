<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return 'HI';
});

Route::controller(ArticleController::class)->group(function () {
    Route::apiResource('articles', ArticleController::class);
})->middleware('guest');


Route::controller(UserController::class)->group(function () {
    Route::apiResource('users', UserController::class);
});