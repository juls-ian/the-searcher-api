<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Http\Resources\CalendarResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CalendarController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Calendar::class);
        return CalendarResource::collection(Calendar::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarRequest $request)
    {
        $this->authorize('create', Calendar::class);
        $validatedCalendar = $request->validated();

        $calendar = Calendar::create($validatedCalendar);
        return CalendarResource::make($calendar);
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        return CalendarResource::make($calendar);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendar $calendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        $this->authorize('update', $calendar);
        $validatedCalendar = $request->validated();
        $calendar->update($validatedCalendar);
        return CalendarResource::make($calendar);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        $this->authorize('delete', $calendar);
        $calendar->delete();
        return response()->json([
            'message' => 'Calendar entry was deleted'
        ]);
    }
}
