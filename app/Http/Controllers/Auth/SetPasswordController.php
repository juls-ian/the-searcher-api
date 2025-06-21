<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;

class SetPasswordController extends Controller
{
    private $token;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'token' => ['required'],  # included in the url of the email
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed', # matching password_confirmation field
                RulesPassword::min(8)->letters()->numbers()->mixedCase()->symbols()

            ]
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([ # allows to mass assign w/o checking $fillable
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                // Send email verification link if user unverified 
                if (!$user->hasVerifiedEmail()) {
                    $user->sendEmailVerificationNotification();
                }

                // Issue sanctum token 
                $this->token = $user->createToken('set-password-token')->plainTextToken;
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json([
                'success' => true,
                'message' => 'Password was set successfully. An email was sent to verify your email',
                'token' => $this->token
            ])
            : response()->json(['success' => false, 'message' => $this->getErrorMessage($status)]);

    }

    private function getErrorMessage($status)
    {
        return match ($status) { # match is similar to switch expression
            Password::INVALID_TOKEN => 'Token is invalid or expired',
            default => 'Unable to set password'
        };
    }
}