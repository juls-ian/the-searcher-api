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