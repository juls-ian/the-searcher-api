<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = $request->authenticate();

        $remember = $request->boolean('remember');

        // Create API token 
        $tokenName = $remember ? 'remember-token' : 'auth-token'; // distinguish two tokens
        $token = $user->createToken($tokenName);

        if ($remember) {
            // Remember me: 30 days 
            $token->accessToken->expires_at = now()->addDays(7);
        } else {
            // Regular login: 1 hour (or use config default)
            $token->accessToken->expires_at = now()->addHours(1);
        }

        $token->accessToken->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at,
                'remember' => $remember
            ]
        ]);

    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successfully'
        ]);
    }


    public function currentUser(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user(),
                'token_expires_at' => $token->expires_at ? $token->expires_at->toISOString() : null,
                'token_name' => $token->name,
                'remember' => $token->name === 'remember-token'
            ]
        ]);
    }


}