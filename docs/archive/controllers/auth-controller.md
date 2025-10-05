# Unused codes in the AuthController

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
## login v.1.0
    public function login(Request $request)
        {
            $request->validate([
                'login' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $login = $request->input('login');
            $password = $request->input('password');

            // Determine if login is email or staff_id
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'staff_id';

            if (!Auth::attempt([$field => $login, 'password' => $password]), true) {
                throw ValidationException::withMessages([
                    'login' => ['The provided credentials are incorrect.'],
                ]);
            }

            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user()
            ]);
        }

## login v.1.1
    public function login(LoginRequest $request)
        {
            $user = $request->authenticate();

            // Create API token 
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        }

## logout v.1.0
    public function logout(Request $request)
        {
            Auth::logout();
            
            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        }

## currentUser v.1.0
    public function currentUser(Request $request)
        {
        $token = $request->user()->currentAccessToken();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user(),
                'token_expires_at' => $token->expires_at ? $token->expires_at->toISOString() : null,
                'token_name' => $token->name,
                'is_remember_token' => $token->name === 'remember-token'
            ]
        ]);
        
        }

## refreshToken
    public function refreshToken(LoginRequest $request)
        {
            $user = $request->user();
            // Create access token 
            $currentAccessToken = $request->user()->currentAccessToken();

            $remember = $request->boolean('remember');

            $user = $request->user();
            $currentToken = $request->user()->currentAccessToken();
            $remember = $request->boolean('remember');

            // Get the original remember state if not provided
            if (!$request->has('remember')) {
                // Check if current token was a long-lived token (assume remember=true if expires > 1 day)
                $remember = $currentToken->expires_at && $currentToken->expires_at->gt(now()->addDay());
            }

            // Revoke current token
            $currentToken->delete();

            // Create new token
            $token = $user->createToken('auth-token');

            // Set expiration based on remember me
            if ($remember) {
                $token->accessToken->expires_at = now()->addDays(30);
            } else {
                $token->accessToken->expires_at = now()->addHours(1);
            }

            $token->accessToken->save();

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_at' => $token->accessToken->expires_at,
                    'remember' => $remember
                ]
            ]);

        }

    
}