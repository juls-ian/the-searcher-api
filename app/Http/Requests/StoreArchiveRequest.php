<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'in:article,user,community-segment,bulletin,editorial-board,issue,multimedia',
            ],
            'archivable_id' => ['nullable', 'integer'],
            'title' => ['required', 'string'],
            'data' => ['required'],
            'archiver_id' => ['required', 'exists:users,id']
        ];

        return $rules;
    }
}
