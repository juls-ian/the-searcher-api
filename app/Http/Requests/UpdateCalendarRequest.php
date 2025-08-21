<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCalendarRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:200'],
            'start_at' => ['sometimes', 'date'],
            'ends_at' => [
                'nullable',
                'date',
                Rule::when(
                    !$this->boolean('is_allday'), // hen NOT all-day apply this rule 
                    ['after:start_at']
                )
            ],
            'is_allday' => ['sometimes', 'boolean'],
            'details' => ['sometimes', 'nullable', 'string'],
            'venue' => ['sometimes', 'nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
            'event_type' => ['sometimes', 'in:release,event,meeting']
        ];
    }

    /**
     * Override Laravel's default validated() to add custom logic 
     * to process the ends_at field
     * If is_allday = true, it auto-fills ends_at = 23:59:59. 
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // If is_allday is true, set ends_at to the last hour of the start date
        if ($validated['is_allday'] ?? false) {
            $startDate = Carbon::parse($validated['start_at']);
            $validated['ends_at'] = $startDate->copy()->endOfDay();
        }

        return $validated;
    }

    public function messages()
    {
        return [
            'ends_at.after' => 'The end date must be after the start date.',
            'event_type.in' => 'The event type must be one of: release, event, or meeting'
        ];
    }
}
