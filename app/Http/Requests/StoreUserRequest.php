<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:30'],
            'last_name' => ['required', 'string', 'max:20'],
            'pen_name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email'],
            'year_level' => ['required', 'string'],
            'course' => ['required', 'string'],
            // 'phone' => ['required', 'regex:/^(\+63|0)\d{10}$/'],
            'phone' => ['required', 'phone:PH'], # needs proganista/laravel-phone
            'board_position' => ['required', 'string'],
            'role' => ['required', 'string'],
            'term' => ['required', 'string'],
            'status' => ['required', 'string'],
            'joined_at' => ['required', 'date'],
            'left_at' => ['nullable', 'date'],
            'profile_pic' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000']
        ];
    }
    
    public function messages()
    {
        return [
            'profile_pic.image' => 'Profile Pic must be a valid image file.',
            'profile_pic.mimes' => 'Profile Pic must be jpeg, png, or webp format',
     
        ];
    }
}