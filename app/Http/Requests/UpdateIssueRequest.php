<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueRequest extends FormRequest
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
            'description' => ['sometimes', 'string'],
            'published_at' => ['sometimes', 'date'],
            'editors' => ['sometimes'],
            'editors.*' => ['string'],
            'writers' => ['sometimes'],
            'writers.*' => ['string'],
            'photojournalists' => ['sometimes'],
            'photojournalists.*' => ['string'],
            'artists' => ['sometimes'],
            'artists.*' => ['string'],
            'layout_artists' => ['sometimes'],
            'layout_artists.*' => ['string'],
            'contributors' => ['sometimes'],
            'contributors.*' => ['string'],
            'issue_file' => [
                'sometimes',
                'file',
                'max:5120', # 50mb 
                'mimes:pdf,epub',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'thumbnail' => ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],

        ];
    }

    public function messages()
    {
        return [
            'thumbnail.image' => 'Cover photo must be a valid image file.',
            'thumbnail.mimes' => 'Cover photo must be jpeg, png, or webp format',
            'thumbnail.max' => 'Cover photo must not exceed 5MB',
        ];
    }
}
