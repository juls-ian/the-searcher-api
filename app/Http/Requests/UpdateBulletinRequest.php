<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulletinRequest extends FormRequest
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
            'title' => ['sometimes', 'string'],
            'category' => ['sometimes', 'in:advisory,announcement'],
            'writer_id' => ['sometimes', 'exists:users,id'],
            'details' => ['sometimes', 'string'],
            'published_at' => ['sometimes', 'null', 'date'],
            'cover_photo' => ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'cover_artist_id' => ['sometimes', 'exists:users,id']
        ];
    }

    public function messages()
    {
        return [
            'cover_photo.image' => 'Cover photo must be a valid image file.',
            'cover_photo.mimes' => 'Cover photo must be jpeg, png, or webp format',
            'cover_photo.max' => 'Cover photo must not exceed 5MB',
        ];
    }
}
