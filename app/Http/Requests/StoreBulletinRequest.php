<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulletinRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'category' => ['required', 'in:advisory,announcement'],
            'writer_id' => ['required', 'exists:users,id'],
            'details' => ['required', 'string'],
            'published_at' => ['sometimes', 'date'],
            'cover_photo' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'cover_artist_id' => ['required', 'exists:users,id']
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
