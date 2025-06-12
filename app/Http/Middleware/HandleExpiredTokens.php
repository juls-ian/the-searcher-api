<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleExpiredTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $token = $request->user()->currentAccessToken();

            if ($token->expires_at && $token->expires_at->isPast()) {

                // If token = expired, delete 
                $token->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Token expired',
                    'error_code' => 'TOKEN_EXPIRED'
                ], 401);
            }
        }
        return $next($request);
    }
}