# Scrapped codes in api route

## v.1
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
use App\Http\Controllers\CommunitySegmentController;
use App\Http\Controllers\MultimediaController;
use App\Http\Middleware\HandleExpiredTokens;
use App\Models\CommunitySegment;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

/**
 * Article routes 
 */
Route::apiResource('articles', ArticleController::class)->middleware('auth:sanctum'); # change to auth:sanctum later

/**
 * User routes
 */
Route::apiResource('users', UserController::class)->middleware('auth:sanctum'); # change to auth:sanctum later

/**
 * Article Category routes 
 */
Route::apiResource('article-categories', ArticleCategoryController::class)->middleware('auth:sanctum'); # change to auth:sanctum later

/**
 * Community Segment routes 
 */
Route::apiResource('community-segments', CommunitySegmentController::class)->middleware('auth:sanctum'); # change to auth:sanctum later

/**
 * Multimedia routes 
 */
Route::apiResource('multimedia', MultimediaController::class)->only(['store', 'update', 'update'])->middleware('auth:sanctum');
Route::apiResource('multimedia', MultimediaController::class);



