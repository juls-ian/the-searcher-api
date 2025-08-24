<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArchiveRequest extends FormRequest
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
                'required',
                'string',
                'in:article,bulletin,issue,multimedia,community-segment',
            ],
            'archivable_id' => ['nullable', 'integer'],
            'title' => ['required', 'string'],
            'data' => ['required'],
        ];

        // Conditional rules for 'data' based on archivable_type 
        switch ($this->input('archivable_type')) {

            // Archive : Article
            case 'article':
                $rules['data.article_category_id'] = ['required', 'integer', 'exists:article_categories,id'];
                $rules['data.writer_id'] = ['required', 'integer', 'exists:users,id'];
                $rules['data.body'] = ['required', 'string'];
                $rules['data.published_at'] = ['required', 'date'];
                $rules['data.cover_photo'] = ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.cover_artist_id'] = ['required', 'integer', 'exists:users,id'];
                $rules['data.credit_type'] = ['required', 'in:photo,graphics,illustration'];
                break;

            // Archive : Multimedia
            case 'multimedia':
                $rules['data.category'] = ['required', 'in:gallery,video,illustration,segment'];
                $rules['data.caption'] = ['required', 'string'];
                $rules['data.published_at'] = ['required', 'date'];
                $rules['data.files'] = ['required', 'array', 'min:1'];
                $rules['data.files.*'] = ['file', 'mimes:jpg,png,jpeg,webp,mp4,avi,mov,wmv,flv,webm', 'max:50000'];
                $rules['data.multimedia_artists_id'] = ['required', 'array', 'exists:users,id'];
                $rules['data.thumbnail'] = ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.thumbnail_artist_id'] = ['required', 'integer', 'min:1'];
                $rules['data.thumbnail_artist_id.*'] = ['integer', 'exists:users,id'];
                $rules['data.credit_type'] = ['required', 'in:photo,graphics,video,illustration'];
                break;

            // Archive: Community Segment
            # segment articles only 
            case 'community-segment':
                $rules['data.writer_id'] = ['required', 'integer',  'exists:users,id'];
                $rules['data.series_type'] = ['in:standalone,series_header,series_issue'];
                $rules['data.series_of'] = [
                    # remove nullable when series_type is series_issue
                    Rule::when($this->input('data.series_type') === 'series_issue', ['required', 'integer']),
                    Rule::when($this->input('data.series_type') !== 'series_issue', ['nullable', 'integer']),
                    Rule::exists('community_segments', 'id')->where(function ($query) {
                        # we only want series headers to be valid parents.
                        $query->where('series_type', 'series_header');
                    }),
                    # forbid setting parent if itself is a header or standalone
                    Rule::prohibitedIf(fn() => in_array($this->input('data.series_type'), ['series_header', 'standalone']))
                ];
                $rules['data.published_at'] = ['sometimes', 'date'];
                $rules['data.series_order'] = [
                    'nullable',
                    'integer',
                    // For series_header: required and must equal 1
                    Rule::when($this->input('data.series_type') === 'series_header', ['required', 'in:1']),
                    // For series_issue: required and must be greater than 1
                    Rule::when($this->input('data.series_type') === 'series_issue', ['required', 'min:2']),
                    // Prohibit for standalone articles and polls
                    Rule::prohibitedIf(fn() => $this->input('data.series_type') === 'standalone'),
                    Rule::prohibitedIf(fn() => $this->input('data.segment_type') === 'poll')
                ];
                $rules['data.body'] =  ['required', 'string'];
                $rules['data.segment_cover'] = ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.cover_artist_id'] = ['required', 'exists:users,id'];
                $rules['data.credit_type'] = ['required', 'in:photo,graphics,illustration'];
                break;

            // Archive: Bulletin
            case 'bulletin':
                $rules['data.category'] =  ['required', 'in:advisory,announcement'];
                $rules['data.writer_id'] = ['required', 'exists:users,id'];
                $rules['data.details'] = ['required', 'string'];
                $rules['data.published_at'] = ['required', 'date'];
                $rules['data.cover_photo'] =  ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
                $rules['data.cover_artist_id'] = ['required', 'exists:users,id'];
                break;

            // Archive: Issue
            case 'issue':
                $rules['data.description'] =  ['required', 'string'];
                $rules['data.published_at'] =  ['required', 'date'];
                $rules['data.editors'] =  ['required'];
                $rules['data.editors.*'] =  ['string'];
                $rules['data.writers'] =  ['required'];
                $rules['data.writers.*'] =  ['string'];
                $rules['data.photojournalists'] =  ['required'];
                $rules['data.photojournalists.*'] =  ['string'];
                $rules['data.artists'] =  ['required'];
                $rules['data.artists.*'] =  ['string'];
                $rules['data.layout_artists'] =  ['required'];
                $rules['data.layout_artists.*'] =  ['string'];
                $rules['data.contributors'] =  ['nullable'];
                $rules['data.contributors.*'] =  ['string'];
                $rules['data.issue_file'] =  [
                    'required',
                    'file',
                    'max:102400', # 100mb    
                    'mimes:pdf,epub',
                    'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];
                $rules['data.thumbnail'] =  ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'];
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
            'data.series_of.required' => 'A series header must be selected when creating a series issue',
            'data.series_of.prohibited' => 'Series parent cannot be set for standalone segments or series headers',
        ];
    }
}
