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
use App\Http\Controllers\BoardPositionController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CommunitySegmentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\SearchController;
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
    Route::delete('articles/{article}/forceDestroy', [ArticleController::class, 'forceDestroy'])->name('articles.forceDestroy');
    Route::post('articles/{article}/restore', [ArticleController::class, 'restore'])->name('articles.restore');
    // Archive route
    Route::post('articles/{id}/archive', [ArticleController::class, 'archive'])->name('articles.archive');
});
// Public archive routes - static routes must be declared before api resource
Route::get('articles/archived', [ArticleController::class, 'archiveIndex'])->name('articles.index-archived');
Route::get('articles/{id}/archived', [ArticleController::class, 'showArchived'])->name('articles.show-archived');
// Public routes
Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);

/**
 * User routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy']);

    Route::delete('users/{user}/forceDestroy', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('user.restore');


    // Term/EdBoard management routes
    Route::post('users/{user}/add-term', [UserController::class, 'addTerm'])->name('users.add-term');
    Route::patch('users/{user}/set-active-term', [UserController::class, 'setCurrentTerm'])->name('users.set-active-term');
    Route::delete('users/{user}/delete-term', [UserController::class, 'deleteTerm'])->name('users.delete-term');
});
Route::apiResource('users', UserController::class)->only(['index', 'show']);

/**
 * Editorial Board route
 */
Route::get('editorial-board/', [UserController::class, 'edBoardIndex'])->name('editorial-board-index');


/**
 * Article Category routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('article-categories', ArticleCategoryController::class)->only(['store', 'update', 'destroy']);

    Route::delete('article-categories/{article_category}/forceDestroy', [ArticleCategoryController::class, 'forceDestroy'])->name('article-categories.forceDestroy');
    Route::post('article-categories/{article_category}/restore', [ArticleCategoryController::class, 'restore'])->name('article-categories.restore');
});
Route::apiResource('article-categories', ArticleCategoryController::class)->only(['index', 'show']);

/**
 * Community Segment routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('community-segments', CommunitySegmentController::class)->only(['store', 'update', 'destroy']);
    Route::delete('community-segments/{community_segment}/forceDestroy', [CommunitySegmentController::class, 'forceDestroy'])
        ->name('community-segments.forceDestroy');
    Route::post('community-segments/{community_segment}/restore', [CommunitySegmentController::class, 'restore'])
        ->name('community-segments.restore');
    Route::post('community-segments/{id}/archive', [CommunitySegmentController::class, 'archive'])
        ->name('community-segments.archive');
});
// Public archive routes
Route::get('community-segments/archived', [CommunitySegmentController::class, 'archiveIndex'])
    ->name('community-segments.archived');
Route::get('community-segments/{id}/archived', [CommunitySegmentController::class, 'showArchived'])
    ->name('community-segments.show-archived');
// Public routes
Route::apiResource('community-segments', CommunitySegmentController::class)->only(['index', 'show']); # public segment routes

/**
 * Multimedia routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('multimedia', MultimediaController::class)->only(['store', 'update', 'destroy']);
    Route::delete('multimedia/{multimedia}/forceDestroy', [MultimediaController::class, 'forceDestroy'])
        ->name('multimedia.forceDestroy');
    Route::post('multimedia/{multimedia}/restore', [MultimediaController::class, 'restore'])->name('multimedia.restore');
    // Archive routes
    Route::post('multimedia/{id}/archive', [MultimediaController::class, 'archive'])->name('multimedia.archive');
});
// Public archive routes
Route::get('multimedia/archived', [MultimediaController::class, 'archiveIndex'])->name('multimedia.index-archived');
Route::get('multimedia/{id}/archived', [MultimediaController::class, 'showArchived'])->name('multimedia.show-archived');
// Public routes
Route::apiResource('multimedia', MultimediaController::class)->only(['index', 'show']); # public multimedia route

/**
 * Issue routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('issues', IssueController::class)->only(['store', 'update', 'destroy']);
    Route::delete('issues/{issue}/forceDestroy', [IssueController::class, 'forceDestroy'])->name('issues.forceDestroy');
    Route::post('issues/{issue}/restore', [IssueController::class, 'restore'])->name('issues.restore');
    Route::post('issues/{id}/archive', [IssueController::class, 'archive'])->name('issues.archive');
});
Route::get('issues/archived', [IssueController::class, 'archiveIndex'])->name('issues.archived');
Route::get('issues/{id}/archived', [IssueController::class, 'showArchived'])->name('issues.show-archived');
Route::apiResource('issues', IssueController::class)->only(['index', 'show']);

/**
 * Bulletin routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('bulletins', BulletinController::class)->only(['store', 'update', 'destroy']);
    Route::delete('bulletins/{bulletin}/forceDestroy', [BulletinController::class, 'forceDestroy'])->name('bulletins.forceDestroy');
    Route::post('bulletins/{bulletin}/restore', [BulletinController::class, 'restore'])->name('bulletins.restore');
    Route::post('bulletins/{id}/archive', [BulletinController::class, 'archive'])->name('bulletins.archive');
});
Route::get('bulletins/archived', [BulletinController::class, 'archiveIndex'])->name('bulletins.archived');
Route::get('bulletins/{id}/archived', [BulletinController::class, 'showArchived'])->name('bulletins.show-archived');
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
    Route::delete('archives/{id}/forceDestroy', [ArchiveController::class, 'forceDestroy'])->name('archives.forceDestroy');
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
 * Board Position routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('board-positions', BoardPositionController::class)->only(['store', 'update', 'destroy']);

    // Additional routes
    Route::delete('board-positions/{board_position}/forceDestroy', [BoardPositionController::class, 'forceDestroy'])->name('board-positions.force-destroy');
    Route::post('board-positions/{board_position}/restore', [BoardPositionController::class, 'restore'])->name('board-positions.restore');
});
Route::apiResource('board-positions', BoardPositionController::class)->only(['index', 'show']);

/**
 * Search
 */
Route::get('/search/archives', [SearchController::class, 'archive'])->name('search.archive');
Route::get('/search', [SearchController::class, 'universal'])->name('search.universal');
Route::get('/search/{model}', [SearchController::class, 'model'])->name('search.model');

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
