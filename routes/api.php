<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ArticleCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CommunitySegmentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MultimediaController;
use App\Http\Middleware\HandleExpiredTokens;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

/**
 * Article routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('articles', ArticleController::class)->only(['store', 'update', 'destroy']);

    // Archive routes 
    Route::get('articles/archived', [ArticleController::class, 'archiveIndex'])->name('articles.index-archived');
    Route::get('articles/{id}/archived', [ArticleController::class, 'showArchived'])->name('articles.show-archived');
    Route::post('articles/{id}/archive', [ArticleController::class, 'archive'])->name('articles.archive');
    Route::delete('articles/{article}/forceDestroy', [ArticleController::class, 'forceDestroy'])->name('articles.forceDestroy');
    Route::post('articles/{article}/restore', [ArticleController::class, 'restore'])->name('articles.restore');
});
Route::apiResource('articles', ArticleController::class)->only(['index', 'show']); # public route

/**
 * User routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy']);

    // Term/EdBoard management routes 
    Route::post('users/{user}/add-term', [UserController::class, 'addTerm'])->name('users.add-term');
    Route::patch('users/{user}/set-active-term', [UserController::class, 'setCurrentTerm'])->name('users.set-active-term');
    Route::delete('users/{user}/delete-term', [UserController::class, 'deleteTerm'])->name('users.delete-term');
});
Route::apiResource('users', UserController::class)->only(['index', 'show']);

/**
 * Article Category routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('article-categories', ArticleCategoryController::class)->only(['store', 'update', 'destroy']);
});
Route::apiResource('article-categories', ArticleCategoryController::class)->only(['index', 'show']);


/**
 * Community Segment routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('community-segments', CommunitySegmentController::class)->only(['store', 'update', 'destroy']);
});
Route::apiResource('community-segments', CommunitySegmentController::class)->only(['index', 'show']); # public segment routes 


/**
 * Multimedia routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('multimedia', MultimediaController::class)->only(['store', 'update', 'destroy']);
});
Route::apiResource('multimedia', MultimediaController::class)->only(['index', 'show']); # public multimedia route

/**
 * Issue routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('issues', IssueController::class)->only(['store', 'update', 'destroy']);
});
Route::apiResource('issues', IssueController::class)->only(['index', 'show']);

/**
 * Bulletin routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('bulletins', BulletinController::class)->only(['store', 'update', 'destroy']);
});
Route::apiResource('bulletins', BulletinController::class)->only(['index', 'show']);

/**
 * Archive routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('archives', ArchiveController::class)->only(['store', 'update', 'destroy']);

    // Additional routes 
    Route::get('archives/trashed/index', [ArchiveController::class, 'showTrashed'])->name('archives.trashed');
    # 'id' because we're not using route model binding
    Route::post('archives/{id}/restore', [ArchiveController::class, 'restore'])->name('archives.restore');
    Route::delete('archives/{id}/forceDestroy', [ArchiveController::class, 'forceDestroy'])->name('archives.force-destroy');
    Route::post('archives/{id}/unarchive', [ArchiveController::class, 'unarchive'])->name('archives.unarchive');
});
Route::apiResource('archives', ArchiveController::class)->only(['index', 'show']);

/**
 * Calendar routes 
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('calendars', CalendarController::class)->only(['store', 'update', 'destroy']);
});
Route::apiResource('calendars', CalendarController::class)->only(['index', 'show']);

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
