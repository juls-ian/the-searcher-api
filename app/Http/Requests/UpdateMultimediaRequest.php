<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMultimediaRequest extends FormRequest
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
            'category' => ['sometimes', 'in:gallery,video,illustration,segment'],
            'caption' => ['sometimes', 'string'],
            'published_at' => ['sometimes',  'date'],
            'files' => ['sometimes'],
            'files.*' => ['image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'multimedia_artists_id.*' => ['sometimes', 'integer', 'exists:users,id'],
            'thumbnail' => ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'thumbnail_artist_id' => ['sometimes', 'integer', 'exists:users,id']

        ];
    }

    public function messages()
    {
        return [
            'files.image' => 'Files must be a valid image file.',
            'thumbnail.image' => 'Thumbnail must be a valid image file.',
            'files.*.mimes' => 'Files must be jpeg, png, or webp format',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, png, or webp format'
        ];
    }
}
