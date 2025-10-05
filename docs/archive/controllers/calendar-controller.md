# Scrapped codes in CalendarController

## store()
### 1.0: initial 
    public function store(StoreCalendarRequest $request)
    {
        // $this->authorize('create', Calendar::class);
        $validatedCalendar = $request->validated();

        // check if key exists in the array && if the value is truthy (not null)
        if (isset($validatedCalendar['ends_at']) && $validatedCalendar['ends_at']) {
            $validatedCalendar['is_allday'] = false;
        } else {
            $validatedCalendar['is_allday'] = true;
        }

        // $validatedCalendar['is_allday'] = empty($validatedCalendar['ends_at']); - shorter version

        $calendar = Calendar::create($validatedCalendar);
        return CalendarResource::make($calendar);
    }
### 1.1: logic for the all_day 
    public function store(StoreCalendarRequest $request)
    {
        $validated = $request->validated();

        if ($validated['is_allday']) {
            // Set ends_at to end of the start_at day
            $validated['ends_at'] = \Carbon\Carbon::parse($validated['start_at'])
                ->endOfDay();
        }

        $calendar = Calendar::create($validated);

        return response()->json($calendar, 201);
    }