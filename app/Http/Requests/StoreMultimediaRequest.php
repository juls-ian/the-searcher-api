<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreMultimediaRequest extends FormRequest
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
            'category' => ['required', 'in:gallery,video,illustration,segment'],
            'caption' => ['required', 'string'],
            'published_at' => ['sometimes', 'date'],
            'files' => ['required'],
            'files.*' => ['image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'multimedia_artists_id.*' => ['required', 'integer', 'exists:users,id'],
            'thumbnail' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'thumbnail_artist_id' => ['required', 'integer', 'exists:users,id'],
            'thumbnail_credit_type' => ['sometimes', 'in:photo,graphics,video,illustration']

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
