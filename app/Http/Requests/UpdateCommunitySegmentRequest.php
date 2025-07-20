<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommunitySegmentRequest extends FormRequest
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
        $rules = [
            'title' => ['sometimes', 'string'],
            'writer_id' => ['sometimes', 'exists:users,id'],
            'series_of' => ['sometimes', 'nullable', 'exists:community_segments,id'],
            'published_at' => ['sometimes', 'date'],
            'segment_cover' => ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'cover_artist_id' => ['sometimes', 'exists:users,id'],
            'cover_caption' => ['sometimes', 'string']
        ];

        if ($this->segment_type === 'article') {
            $rules['series_order'] = [
                'sometimes',
                'nullable',
                'exists:community_segments,id'
            ];
            $rules['body'] = ['sometimes', 'string'];

        } else if ($this->segment_type === 'poll') {
            $rules['series_order'] = [
                Rule::prohibitedIf($this->segment_type === 'poll'),
            ];
            $rules['question'] = ['sometimes', 'string', 'max:500'];
            $rules['options'] = ['sometimes', 'array', 'min: 2', 'max:10'];
            $rules['options.*'] = ['sometimes', 'string', 'max:250'];
            $rules['ends_at'] = ['sometimes', 'date', 'after:today'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'segment_cover.image' => 'Cover photo must be a valid image file.',
            'segment_cover.mimes' => 'Cover photo must be jpeg, png, gif, or webp format',
            'segment_cover.max' => 'Cover photo must not exceed 5MB',
            'thumbnail.image' => 'Thumbnail must be a valid image file.',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, png, gif, or webp format',
            'thumbnail.max' => 'Thumbnail must not exceed 5MB',
        ];
    }

    # series_of mutator
    public function setSeriesOfAttribute($value)
    {
        $this->attributes['series_of'] = ($this->segment_type === 'article') ? $value : null;
    }
}