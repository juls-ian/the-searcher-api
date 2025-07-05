<?php

use App\Http\Middleware\HandleExpiredTokens;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register expired token Middleware
        $middleware->alias([
            'check.expired.tokens' => HandleExpiredTokens::class
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handler 1: Authorization Exceptions 
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {

            // API requests handler (requests that expect JSON)
            if ($request->expectsJson()) {
                $user = $request->user();

                // Default message 
                $message = 'You do not have permission to perform this action';

                // Role specific messages 
                if ($user) {
                    $message = match ($user->role) {
                        'staff' => 'Staff writers have limited permissions. Contact an admin or editor for assistance.',
                        'editor' => 'This action requires admin permissions.',
                        'admin' => 'Access denied.', # shouldn't happen but just in case 
                        default => 'You do not have permission to perform this action. '
                    };
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error' => 'Forbidden'
                ], 403);
            };

            // Non-API requests shall be handled by Laravel 
            return null;
        });



    })->create();