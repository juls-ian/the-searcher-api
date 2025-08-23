<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArchiveRequest extends FormRequest
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
        $rules =  [
            'archivable_type' => [
                'sometimes',
                'string',
                'in:article,bulletin,issue,multimedia,community-segment',
            ],
            'archivable_id' => ['nullable', 'integer'],
            'title' => ['sometimes', 'string'],
            'data' => ['sometimes'],
        ];

        // Conditional rules for 'data' based on archivable_type 
        switch ($this->input('archivable_type')) {

            // Archive : Article
            case 'article':
                $rules['data.article_category_id'] = ['sometimes', 'integer', 'exists:article_categories,id'];
                $rules['data.writer_id'] = ['sometimes', 'integer', 'exists:users,id'];
                $rules['data.body'] = ['sometimes', 'string'];
                $rules['data.published_at'] = ['sometimes', 'date'];
                $rules['data.cover_photo'] = ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.cover_artist_id'] = ['sometimes', 'integer', 'exists:users,id'];
                break;

            // Archive : Multimedia
            case 'multimedia':
                $rules['data.category'] = ['sometimes', 'in:gallery,video,illustration,segment'];
                $rules['data.caption'] = ['sometimes', 'string'];
                $rules['data.published_at'] = ['sometimes', 'date'];
                $rules['data.files'] = ['sometimes'];
                $rules['data.files.*'] = ['image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.multimedia_artists_id'] = ['sometimes', 'integer', 'exists:users,id'];
                $rules['data.thumbnail'] = ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.thumbnail_artist_id'] = ['sometimes', 'integer', 'exists:users,id'];
                break;

            // Archive: Community Segment
            # segment articles only 
            case 'community-segment':
                $rules['data.writer_id'] = ['sometimes', 'integer',  'exists:users,id'];
                $rules['data.series_type'] = ['in:standalone,series_header,series_issue'];
                $rules['data.series_of'] = [
                    # remove nullable when series_type is series_issue
                    Rule::when($this->input('data.series_type') === 'series_issue', ['sometimes', 'integer']),
                    Rule::when($this->input('data.series_type') !== 'series_issue', ['sometimes', 'nullable', 'integer']),
                    Rule::exists('community_segments', 'id')->where(function ($query) {
                        # we only want series headers to be valid parents.
                        $query->where('series_type', 'series_header');
                    }),
                    # forbid setting parent if itself is a header or standalone
                    Rule::prohibitedIf(fn() => in_array($this->input('data.series_type'), ['series_header', 'standalone']))
                ];
                $rules['data.published_at'] = ['sometimes', 'date'];
                $rules['data.series_order'] = [
                    'sometimes',
                    'nullable',
                    'integer',
                    // For series_header: sometimes and must equal 1
                    Rule::when($this->input('data.series_type') === 'series_header', ['sometimes', 'in:1']),
                    // For series_issue: sometimes and must be greater than 1
                    Rule::when($this->input('data.series_type') === 'series_issue', ['sometimes', 'min:2']),
                    // Prohibit for standalone articles and polls
                    Rule::prohibitedIf(fn() => $this->input('data.series_type') === 'standalone'),
                    Rule::prohibitedIf(fn() => $this->input('data.segment_type') === 'poll')
                ];
                $rules['data.body'] =  ['sometimes', 'string'];
                $rules['data.segment_cover'] = ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.credit_type'] = ['sometimes', 'in:photo,graphics'];
                $rules['data.cover_artist_id'] = ['sometimes', 'exists:users,id'];
                break;

            // Archive: Bulletin
            case 'bulletin':
                $rules['data.category'] =  ['sometimes', 'in:advisory,announcement'];
                $rules['data.writer_id'] = ['sometimes', 'exists:users,id'];
                $rules['data.details'] = ['sometimes', 'string'];
                $rules['data.published_at'] = ['sometimes', 'date'];
                $rules['data.cover_photo'] =  ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.cover_artist_id'] = ['sometimes', 'exists:users,id'];
                break;

            // Archive: Issue
            case 'issue':
                $rules['data.description'] =  ['sometimes', 'string'];
                $rules['data.published_at'] =  ['sometimes', 'date'];
                $rules['data.editors'] =  ['sometimes'];
                $rules['data.editors.*'] =  ['string'];
                $rules['data.writers'] =  ['sometimes'];
                $rules['data.writers.*'] =  ['string'];
                $rules['data.photojournalists'] =  ['sometimes'];
                $rules['data.photojournalists.*'] =  ['string'];
                $rules['data.artists'] =  ['sometimes'];
                $rules['data.artists.*'] =  ['string'];
                $rules['data.layout_artists'] =  ['sometimes'];
                $rules['data.layout_artists.*'] =  ['string'];
                $rules['data.contributors'] =  ['sometimes', 'nullable'];
                $rules['data.contributors.*'] =  ['string'];
                $rules['data.issue_file'] =  [
                    'sometimes',
                    'file',
                    'max:102400', # 100mb    
                    'mimes:pdf,epub',
                    'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];
                $rules['data.thumbnail'] =  ['sometimes', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                break;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'archivable_type.in' => 'Archivable must be article, multimedia, issue, community segment articles, or bulletin only',
            'data.series_order.in' => 'Series header must have series order of 1',
            'data.series_order.min' => 'Series issues must have series order greater than 1',
            'data.credit_type.in' => 'Credits type should only be photo or graphics',
            'data.series_of.sometimes' => 'A series header must be selected when creating a series issue',
            'data.series_of.prohibited' => 'Series parent cannot be set for standalone segments or series headers',
        ];
    }
}
