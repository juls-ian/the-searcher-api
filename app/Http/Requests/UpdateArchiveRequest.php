<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'in:article,user,community-segment,bulletin,editorial-board,issue,multimedia',
            ],
            'archivable_id' => ['sometimes', 'nullable', 'integer'],
            'title' => ['sometimes', 'string'],
            'data' => ['sometimes'],
            'archiver_id' => ['sometimes', 'exists:users,id']
        ];

        return $rules;
    }
}
