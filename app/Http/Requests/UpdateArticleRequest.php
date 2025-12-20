<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'article_category_id' => ['sometimes', 'integer', 'exists:article_categories,id'],
            'writer_id' => ['sometimes', 'integer', 'exists:users,id'],
            'body' => ['sometimes', 'string'],
            'published_at' => ['sometimes', 'date'],
            'is_live' => ['sometimes', 'boolean'],
            'is_header' => ['sometimes',  'boolean'],
            'series_id' => ['sometimes', 'integer', 'nullable', 'exists:articles,id'],
            'cover_photo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'cover_caption' => ['sometimes', 'string'],
            'cover_artist_id' => ['sometimes', 'integer', 'exists:users,id'],
            'cover_credit_type' => ['sometimes', 'in:photo,graphics,illustration'],
            'thumbnail_same_as_cover' => ['sometimes', 'boolean'],
            'thumbnail' => ['sometimes', 'required_if:thumbnail_same_as_cover,false', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            // 'thumbnail' => ['required_if:thumbnail_same_as_cover,false'],
            'thumbnail_artist_id' => ['sometimes', 'integer', 'exists:users,id', 'required_if:thumbnail_same_as_cover,false'],
            'archived_at' => ['sometimes', 'nullable', 'date'],
            'add_to_ticker' => ['sometimes', 'nullable', 'boolean'],
            'ticker_expires_at' => ['sometimes', 'nullable', 'date', 'after:now,required_if:add_to_ticker,true']

        ];
    }

    public function messages()
    {
        return [
            'cover_photo.image' => 'Cover photo must be a valid image file.',
            'cover_photo.mimes' => 'Cover photo must be jpeg, png, or webp format',
            'cover_photo.max' => 'Cover photo must not exceed 5MB',
            'thumbnail.image' => 'Thumbnail must be a valid image file.',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, png, or webp format',
            'thumbnail.max' => 'Thumbnail must not exceed 5MB',
            'thumbnail.required_without' => 'Thumbnail when not using the same as cover photo.',
            'ticker_expires_at.after' => 'Ticker expiration must be in the future.',
            'ticker_expires_at.required_if' => 'Ticker expiration date when adding to ticker.',
        ];
    }
}
