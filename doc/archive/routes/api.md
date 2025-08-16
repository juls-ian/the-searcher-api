# Unused codes in the api route


## Auth routes
Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', HandleExpiredTokens::class])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'currentUser']);

    });

## Article routes 
// Article routes Add commentMore actions
Route::controller(ArticleController::class)->group(function () {
    Route::apiResource('articles', ArticleController::class);
})->middleware('guest');

// User routes
Route::controller(UserController::class)->group(function () {
    Route::apiResource('users', UserController::class);
});


## Email Verification route
Route::prefix('email')->middleware('auth:sanctum')->group(function () {

    // Email verification notice 
    Route::post('/verify', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'User is already verified'], 400);
        }

        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'A verification email was sent.']);

    })->middleware(['throttle:5,1'])
        ->name('verification.notice');

    // Email verification handler
    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return response()->json(['message' => 'Email successfully verified']);
    })->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resending verification email 
    Route::post('/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link resent']);
    })->middleware('throttle:6,1')->name('verification.send');

});