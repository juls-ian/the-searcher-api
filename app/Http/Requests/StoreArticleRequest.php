<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'article_category_id' => ['required', 'exists:article_categories,id'],
            'writer_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_live' => ['nullable', 'boolean'],
            'is_header' => ['nullable', 'boolean'],
            'series_id' => ['nullable', 'exists:articles,id'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            // 'cover_photo' => 'required|file,image',
            'cover_caption' => ['required', 'string'],
            'cover_artist_id' => ['required', 'exists:users,id'],
            'thumbnail_same_as_cover' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'required_if:thumbnail_same_as_cover,false', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            // 'thumbnail' => 'nullable|required_without:thumbnail_same_as_cover|file|image',
            'thumbnail_artist_id' => ['nullable', 'exists:users,id'],
            'is_archived' => ['nullable', 'boolean'],
            'archived_at' => ['nullable', 'date'],
            'add_to_ticker' => ['nullable', 'boolean'],
            'ticker_expires_at' => ['nullable', 'date', 'after:now,required_if:add_to_ticker,true']
        ];
    }

    public function messages()
    {
        return [
            'cover_photo.image' => 'Cover photo must be a valid image file.',
            'cover_photo.mimes' => 'Cover photo must be jpeg, png, gif, or webp format',
            'cover_photo.max' => 'Cover photo must not exceed 5MB',
            'thumbnail.image' => 'Thumbnail must be a valid image file.',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, png, gif, or webp format',
            'thumbnail.max' => 'Thumbnail must not exceed 5MB',
            'ticker_expires_at.after' => 'Ticker expiration must be in the future.',
            'ticker_expires_at.required_if' => 'Ticker expiration date is required when adding to ticker.',
        ];
    }
}



/**
 * Must be revised on later part:
 * 1. cover_photo
 * 2. thumbnail
 */