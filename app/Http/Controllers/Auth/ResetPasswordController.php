<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;

class ResetPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'token' => ['required'], # included in the url of the email
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
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'message' => 'Password has been reset'])
            : response()->json(['success' => false, 'message' => $this->getErrorMessage($status)], 400);


    }

    private function getErrorMessage($status)
    {

        return match ($status) {
            Password::INVALID_TOKEN => 'Token is invalid or expired',
            default => 'Unable to reset password'
        };
    }
}