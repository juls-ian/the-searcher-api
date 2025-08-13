<?php

namespace App\Observers;

use App\Models\Bulletin;
use Illuminate\Support\Str;

class BulletinObserver
{
    /**
     * Handle the Bulletin "created" event.
     */
    public function created(Bulletin $bulletin): void
    {
        //
    }

    public function creating(Bulletin $bulletin)
    {
        $baseSlug = Str::slug($bulletin->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        # keep generating slug until it's unique 
        while (Bulletin::where('slug', $slug)->exists()) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}";
        }
        $bulletin->slug = $slug;
    }

    public function updating(Bulletin $bulletin)
    {
        if ($bulletin->isDirty('title')) {

            $baseSlug = Str::slug($bulletin->title);
            $slugDate = now()->format('Y-m-d');
            $slug = "{$baseSlug}-{$slugDate}";

            # check if slug exists 
            while (Bulletin::where('slug', $slug)
                ->where('id', '!=', $bulletin->id) # to avoid comparing slug to itself 
                ->exists()

            ) {
                $randomId = Str::lower(Str::random(8));
                $slug = "{$slug}-{$randomId}";
            }
            $bulletin->slug = $slug; # final base value
        }
    }

    /**
     * Handle the Bulletin "updated" event.
     */
    public function updated(Bulletin $bulletin): void
    {
        //
    }

    /**
     * Handle the Bulletin "deleted" event.
     */
    public function deleted(Bulletin $bulletin): void
    {
        //
    }

    /**
     * Handle the Bulletin "restored" event.
     */
    public function restored(Bulletin $bulletin): void
    {
        //
    }

    /**
     * Handle the Bulletin "force deleted" event.
     */
    public function forceDeleted(Bulletin $bulletin): void
    {
        //
    }
}
