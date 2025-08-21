<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommunitySegmentRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'segment_type' => ['required', 'in:article,poll'],
            'writer_id' => ['required', 'integer', 'integer', 'exists:users,id'],
            'series_of' => ['nullable', 'exists:community_segments,id'],
            'published_at' => ['sometimes', 'date'],
            'series_order' => ['nullable', 'integer', 'min:1'],
            'segment_cover' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'cover_artist_id' => ['required', 'exists:users,id'],
            'cover_caption' => ['required', 'string']
        ];

        if ($this->segment_type === 'article') {
            $rules['series_order'] = [
                'nullable',
                'exists:community_segments,id'
            ];
            $rules['body'] = ['required', 'string'];
        } else if ($this->segment_type === 'poll') {
            $rules['series_order'] = [
                Rule::prohibitedIf($this->segment_type === 'poll'),
            ];
            $rules['question'] = ['required', 'string', 'max:500'];
            $rules['options'] = ['required', 'array', 'min: 2', 'max:10'];
            $rules['options.*'] = ['required', 'string', 'max:250'];
            $rules['ends_at'] = ['required', 'date', 'after:today'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'segment_type.in' => 'Segment type should only be article or poll',
            'series_order.prohibited' => 'Series order is not allowed for polls',
            'segment_cover.image' => 'Cover photo must be a valid image file.',
            'segment_cover.mimes' => 'Cover photo must be jpeg, png, or webp format',
            'segment_cover.max' => 'Cover photo must not exceed 5MB',
            'thumbnail.image' => 'Thumbnail must be a valid image file.',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, png, or webp format',
            'thumbnail.max' => 'Thumbnail must not exceed 5MB',
        ];
    }
}
