<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'editors' => ['required'],
            'editors.*' => ['string'],
            'writers' => ['required'],
            'writers.*' => ['string'],
            'photojournalists' => ['required'],
            'photojournalists.*' => ['string'],
            'artists' => ['required'],
            'artists.*' => ['string'],
            'layout_artists' => ['required'],
            'layout_artists.*' => ['string'],
            'contributors' => ['required'],
            'contributors.*' => ['string'],
            'issue_file' => [
                'required',
                'file',
                'max:102400', # 100mb    
                'mimes:pdf,epub',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'thumbnail' => ['required', 'image', 'mimes:jpg,png,jpeg,webp', 'max:5000'],
        ];
    }
}
