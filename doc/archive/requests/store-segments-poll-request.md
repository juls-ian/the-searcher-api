# Unused codes in the StoreSegmentsArticleRequest

## v.1
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSegmentsPollRequest extends StoreCommunitySegmentRequest
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
        return array_merge($this->baseRules(), [
            'question' => ['required', 'string', 'max:500'],
            'options' => ['required', 'array', 'min: 2', 'max:10'],
            'options.*' => ['required', 'string', 'max:250'],
            'ends_at' => ['required', 'date', 'after:today']
        ]);
        //
    }
}
