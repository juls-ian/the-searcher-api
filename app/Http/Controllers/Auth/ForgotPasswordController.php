<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // Sending reset link 
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Returning response 
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => 'Password reset link sent.'], 200)
            : response()->json(['success' => false, 'message' => $this->getErrorMessage($status)], 400);
    }

    private function getErrorMessage($status)
    {
        return match ($status) {
            Password::INVALID_USER => 'User not found',
            default => 'Unable to send password reset link'
        };
    }
}