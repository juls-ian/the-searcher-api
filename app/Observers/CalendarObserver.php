<?php

namespace App\Observers;

use App\Models\Calendar;
use Illuminate\Support\Str;

class CalendarObserver
{
    /**
     * Handle the Calendar "created" event.
     */
    public function created(Calendar $calendar): void
    {
        //
    }

    public function creating(Calendar $calendar)
    {
        $baseSlug = Str::slug($calendar->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        while (Calendar::where('slug', $slug)->exists()) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}";
        }
        $calendar->slug = $slug;
    }

    public function updating(Calendar $calendar)
    {
        $baseSlug = Str::slug($calendar->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        while (Calendar::where('slug', $slug)
            ->where('id', '!=', $calendar->id) #ensure not to compare to itself 
            ->exists()
        ) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}";
        }
        return $calendar->slug = $slug;
    }
    /**
     * Handle the Calendar "updated" event.
     */
    public function updated(Calendar $calendar): void
    {
        //
    }

    /**
     * Handle the Calendar "deleted" event.
     */
    public function deleted(Calendar $calendar): void
    {
        //
    }

    /**
     * Handle the Calendar "restored" event.
     */
    public function restored(Calendar $calendar): void
    {
        //
    }

    /**
     * Handle the Calendar "force deleted" event.
     */
    public function forceDeleted(Calendar $calendar): void
    {
        //
    }
}
