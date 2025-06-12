# Unused codes in the api route



Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', HandleExpiredTokens::class])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'currentUser']);

    });
