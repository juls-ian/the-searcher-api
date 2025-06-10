<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
            'login' => ['required', 'string'],
            'password' => ['required', 'string']
        ];
    }


    public function authenticate()
    {

        $this->ensureIsNotRateLimited();

        $login = $this->input('login');
        $password = $this->input('password');

        // determine if login is email or staff id 
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'staff_id';

        // authenticate user 
        if (!Auth::attempt([$field => $login, 'password' => $password], true)) {

            // record failed attempt & increment rate limiter count 
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect']
            ]);
        }

        // if successful clear the rate limiter attempts 
        RateLimiter::clear($this->throttleKey());
        return Auth::user(); // return current auth user 

    }

    // Ensure that the user/request is not rate-limited; otherwise, throw a ValidationException 
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60)
            ])
        ]);
    }

    // Generate throttle key 
    public function throttleKey()
    {
        return Str::transliterate(Str::lower($this->string('login')) . '|' . $this->ip());
    }
}