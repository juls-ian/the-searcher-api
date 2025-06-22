<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Email verification notice 
     */
    function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'User is already verified']);
        }

        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'A verification email was sent.']);
    }

    /**
     * Email verification handler 
     */
    function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return response()->json(['message' => 'Email successfully verified']);
    }

    /**
     * Resending email verification
     */
    function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link resent']);
    }
}