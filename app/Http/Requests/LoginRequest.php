<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emailOrId' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean']
        ];
    }

    /**
     * Authenticate user upon login
     * @return User
     */
    public function authenticate()
    {

        $this->ensureIsNotRateLimited();

        $login = $this->input('emailOrId');
        $password = $this->input('password');

        // Determine if login is email or staff id 
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'staff_id';

        // check user existence 
        $user = User::where($field, $login)->first();

        // Authenticate login 
        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'emailOrId' => ['The credentials entered are incorrect']
            ]);
        }

        // Authenticate password 
        if (!Hash::check($password, $user->password)) {

            // record failed attempt & increment rate limiter count 
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => ['The password entered is incorrect']
            ]);
        }

        Auth::login($user);

        // If successful clear the rate limiter attempts 
        RateLimiter::clear($this->throttleKey());
        return $user; // return current auth user 

    }

    /**
     * Ensure that the user/request is not rate-limited; otherwise, throw a ValidationException 
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'emailOrId' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60)
            ])
        ]);
    }

    /**
     * Generate throttle key
     * @return string
     */
    public function throttleKey()
    {
        return Str::transliterate(Str::lower($this->string('emailOrId')) . '|' . $this->ip());
    }
}
