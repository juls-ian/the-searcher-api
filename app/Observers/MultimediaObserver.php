<?php

namespace App\Observers;

use App\Models\Multimedia;
use Illuminate\Support\Facades\Log;
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
        $baseSlug = Str::slug($multimedia->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        // Keep generating slug if it's not unique 
        while (Multimedia::where('slug', $slug)->exists()) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$baseSlug}-{$slugDate}-{$randomId}";
        }
        $multimedia->slug = $slug; # base final value 
    }

    /**
     * Runs before a new multimedia is updated in database
     */
    public function updating(Multimedia $multimedia)
    {
        if ($multimedia->isDirty('title')) { # check of media is modified 
            $baseSlug = Str::slug($multimedia->title);
            $slugDate = now()->format('Y-m-d');
            $slug = "{$baseSlug}-{$slugDate}";

            // Check if any other multimedia has the slug 
            while (
                Multimedia::where('slug', $slug) # check if slug exists
                ->where('id', '!=', $multimedia->id) # ensure not to compare slug to itself
                ->exists()
            ) {
                $randomId = Str::lower(Str::random(8));
                $slug =  "{$slug}-{$randomId}";
            }
            $multimedia->slug = $slug; # final base value 
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
