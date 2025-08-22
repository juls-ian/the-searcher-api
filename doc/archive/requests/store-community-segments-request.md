# Unused codes in the StoreCommunitySegmentsRequest

## v.1
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        protected function baseRules()
        {
            return [
                'title' => ['required', 'string'],
                'segment_type' => ['required', 'in:article,poll'],
                'writer_id' => ['required', 'exists:users,id'],
                'series_of' => [
                'nullable',
                'exists:community_segments,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->segment_type !== 'article') {
                        $fail('Series can only be set articles');
                            }
                        }
                    ],
                'published_at' => ['required', 'date'],
                'series_order' => ['nullable', 'integer', 'min:1'],
                'segment_cover' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
                'cover_artist_id' => ['required', 'exists:users,id'],
                'cover_caption' => ['required', 'string']
            ];
        }
    }

    
}

## v.2: using this->input
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
            'writer_id' => ['required', 'integer', 'exists:users,id'], // Removed duplicate 'integer'
            'series_type' => [
                'in:standalone,series_header,series_issue',
                Rule::when($this->input('segment_type') === 'article', ['required']),
                Rule::prohibitedIf(fn() => $this->input('segment_type') === 'poll')
            ],
            'series_of' => [
                Rule::when($this->input('series_type') === 'series_issue', ['required', 'integer']),
                Rule::when($this->input('series_type') !== 'series_issue', ['nullable', 'integer']),
                Rule::exists('community_segments', 'id')->where(function ($query) {
                    $query->where('series_type', 'series_header');
                }),
                Rule::prohibitedIf(fn() => in_array($this->input('series_type'), ['series_header', 'standalone']))
            ],
            'series_order' => [
                'nullable',
                'integer',
                Rule::when($this->input('series_type') === 'series_issue', ['required', 'min:2']),
                Rule::prohibitedIf(fn() => in_array($this->input('series_type'), ['standalone', 'series_header'])),
                Rule::prohibitedIf(fn() => $this->input('segment_type') === 'poll')
            ],
            'published_at' => ['sometimes', 'date'],
            'segment_cover' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
            'credit_type' => ['required', 'in:photo,graphics'],
            'cover_artist_id' => ['required', 'exists:users,id'],
            'cover_caption' => ['nullable', 'string']
        ];

        if ($this->segment_type === 'article') {
            $rules['body'] = ['required', 'string'];
            // Remove the problematic series_order override for articles
        } else if ($this->segment_type === 'poll') {
            $rules['question'] = ['required', 'string', 'max:500'];
            $rules['options'] = ['required', 'array', 'min:2', 'max:10']; // Fixed space in 'min: 2'
            $rules['options.*'] = ['required', 'string', 'max:250'];
            $rules['ends_at'] = ['required', 'date', 'after:today'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'segment_type.in' => 'Segment type should only be article or poll',
            'series_type.prohibited' => 'Series type is only valid for article segments',
            'series_order.prohibited' => 'Series order is not allowed for polls or standalone segments',
            'series_of.required' => 'A series header must be selected when creating a series issue',
            'series_of.prohibited' => 'Series parent cannot be set for standalone segments or series headers',
            'credit_type.in' => 'Credits type should only be photo or graphics',
            'segment_cover.image' => 'Cover photo must be a valid image file.',
            'segment_cover.mimes' => 'Cover photo must be jpeg, png, or webp format',
            'segment_cover.max' => 'Cover photo must not exceed 5MB',
            // Removed duplicate error messages
        ];
    }
}