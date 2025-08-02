<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => ['sometimes', 'string', 'max:30'],
            'last_name' => ['sometimes', 'string', 'max:20'],
            'pen_name' => ['sometimes', 'string', 'max:30'],
            'email' => ['sometimes', 'email'],
            'year_level' => ['sometimes', 'string'],
            'course' => ['sometimes', 'string'],
            'phone' => ['sometimes', 'regex:/^(\+63|0)\d{10}$/'],
            'board_position' => ['sometimes', 'string'],
            'role' => ['sometimes', 'string'],
            'term' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'joined_at' => ['sometimes', 'date'],
            'left_at' => ['sometimes', 'nullable', 'date'],
            'profile_pic' => ['sometimes', 'nullable', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000']
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