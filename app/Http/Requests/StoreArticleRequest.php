<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'article_category_id' => ['required', 'integer', 'exists:article_categories,id'],
            'writer_id' => ['required', 'integer', 'exists:users,id'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_live' => ['nullable', 'boolean'],
            'is_header' => ['nullable', 'boolean', 'prohibited_if:is_live,false'],
            'series_id' => [
                'nullable',
                'integer',
                'prohibited_if:is_header,true',
                'prohibited_if:is_live,false',
                Rule::exists('articles', 'id')->where(function ($query) {
                    $query->where('is_header', true);
                })
            ],
            'cover_photo' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            // 'cover_photo' => 'required|file,image',
            'cover_caption' => ['required', 'string'],
            'cover_artist_id' => ['required', 'integer', 'exists:users,id'],
            'cover_credit_type' => ['nullable', 'in:photo,graphics,illustration'],
            'thumbnail_same_as_cover' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'required_if:thumbnail_same_as_cover,false', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            // 'thumbnail' => 'nullable|required_without:thumbnail_same_as_cover|file|image',
            'thumbnail_artist_id' => ['nullable', 'integer', 'exists:users,id'],
            'archived_at' => ['nullable', 'date'],
            'add_to_ticker' => ['nullable', 'nullable', 'boolean'],
            'ticker_expires_at' => ['nullable', 'date', 'after:now']
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
            'ticker_expires_at.after' => 'Ticker expiration must be in the future.',
            'ticker_expires_at.required_if' => 'Ticker expiration date is required when adding to ticker.',
            'is_header.required_if' => 'The header field is required when the article is live.',
            'is_header.prohibited_if' => 'Cannot set header when article is not live.',
            'series_id.prohibited_if' => 'The series id cannot be set when the article is a header or when the article is not live.',
            'series_id.exists' => 'The selected series must be a valid header article.',
        ];
    }
}



/**
 * Must be revised on later part:
 * 1. cover_photo
 * 2. thumbnail
 */
