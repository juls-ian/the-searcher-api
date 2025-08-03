<?php

namespace App\Observers;

use App\Models\Multimedia;
use Illuminate\Support\Str;

class MultimediaObserver
{
    /**
     * Handle the Multimedia "created" event.
     */
    public function created(Multimedia $multimedia): void
    {
        //
    }

    /**
     * Runs before a new multimedia is saved in database
     */
    public function creating(Multimedia $multimedia)
    {

        $slug = Str::slug($multimedia->title); # convert title into slug 
        $originalSlug = $slug;
        $count = 1;

        // Check if slug exists in db 
        while (Multimedia::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++; # append count if slug exists
        }

        $multimedia->slug = $slug; # final value 
    }

    /**
     * Runs before a new multimedia is updated in database
     */
    public function updating(Multimedia $multimedia)
    {
        if ($multimedia->isDirty('title')) { # check of media is modified 
            $slug = Str::slug($multimedia->title);
            $originalSlug = $slug;
            $count = 1;

            // Check if any other multimedia has the slug 
            while (
                Multimedia::where('slug', $slug) # check if slug exists
                ->where('id', '!=', $multimedia->id) # ensure not to compare to itself
                ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }
            $multimedia->slug = $slug; # final value 
        }
    }

    /**
     * Handle the Multimedia "updated" event.
     */
    public function updated(Multimedia $multimedia): void
    {
        //
    }

    /**
     * Handle the Multimedia "deleted" event.
     */
    public function deleted(Multimedia $multimedia): void
    {
        //
    }

    /**
     * Handle the Multimedia "restored" event.
     */
    public function restored(Multimedia $multimedia): void
    {
        //
    }

    /**
     * Handle the Multimedia "force deleted" event.
     */
    public function forceDeleted(Multimedia $multimedia): void
    {
        //
    }
}
