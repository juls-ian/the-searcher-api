# Scrapped codes in the StoreCalendarRequest

## rules()
### 1.0: initial
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'start_at' => ['required', 'date'],
            'ends_at' => [
                'nullable',
                'date',
                'after:start_at',
                function ($attribute, $value, $fail) {
                    if (!request()->boolean('is_allday') && !$value) {
                        $fail('The ends_at field is required when event is not all-day');
                    }

                    if (!request()->boolean('is_allday') && $value <= request()->input('start_at')) {
                        $fail('The ends_at must be after the start at');
                    }
                }
            ],
            'is_allday' => ['required', 'boolean'],
            'venue' => ['nullable', 'string'],
            'is_public' => ['boolean'],
            'event_type' => ['required', 'in:release,event,meeting']
        ];
    }
### 1.1: other closure function 
   public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'start_at' => ['required', 'date'],
            'ends_at' => [
                'nullable',
                'date',
                'after:start_at',
                // Close/anonymous function
                /**
                 * $attribute = the field name being validated (in this case "ends_at")
                 * $value = the actual value being validated (the ends_at date value)
                 * $fail = a callback function called to make the validation fail
                 */
                function ($attribute, $value, $fail) {
                    # skip validation if is_allday is true 
                    if ($this->boolean('is_allday')) {
                        return;
                    }

                    $startAt = $this->input('start_at');
                    if ($value && $startAt && strtotime($value) <= $strtotime($startAt)) {
                        $fail('The end date must be after the start date');
                    }
                }
            ],
            'is_allday' => ['nullable', 'boolean'],
            'details' => ['nullable', 'string'],
            'venue' => ['nullable', 'string'],
            'is_public' => ['nullable', 'boolean'],
            'event_type' => ['required', 'in:release,event,meeting']
        ];
    }

## detectAllDayEvent()
### 1.0: initial version
  /**
     * Decide whether an event should be considered “all-day” 
     * based on the input (start_at, ends_at, and optionally is_allday).
     * If is_allday is missing, it calls detectAllDayEvent().
     */
    protected function detectAllDayEvent(array $validated)
    {

        // Case 1: if all_day exists then it won't be override
        if (isset($validated['is_allday'])) {
            return (bool) $validated['is_allday'];
        }

        // Case 2: if no ends_at, assume all day 
        if (empty($validated['ends_at'])) {
            return true;
        }

        // Case 3: If start_at and ends_at span exactly within the day 
        $start = Carbon::parse($validated['start_at']);
        $end = Carbon::parse($validated['ends_at']);

        return $start->isSameDay($end) &&
            $start->isStartOfDay() &&
            $end->isEndOfDay();
    }