<?php

namespace App\Observers;

use App\Models\CommunitySegment;
use Illuminate\Support\Str;

class CommunitySegmentObserver
{
    /**
     * Handle the CommunitySegment "created" event.
     */
    public function created(CommunitySegment $communitySegment): void
    {
        //
    }

    /**
     * Runs before data is saved in db
     */
    public function creating(CommunitySegment $communitySegment)
    {
        $baseSlug = Str::slug($communitySegment->title); # convert title to slug 
        $slugDate = now()->format('Y-m-d'); # orig slug 
        $slug = "{$baseSlug}-{$slugDate}";

        // Check if the same slug exists in db 
        while (CommunitySegment::where('slug', $slug)->exists()) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}"; # append count it slug exists
        }

        $communitySegment->slug = $slug; # final value 
    }

    public function updating(CommunitySegment $communitySegment)
    {
        if ($communitySegment->isDirty('title')) {
            $baseSlug = Str::slug($communitySegment->title);
            $slugDate = now()->format('Y-m-d'); # orig slug 
            $slug = "{$baseSlug}-{$slugDate}";

            // Check other segments if they have this slug 
            while (
                CommunitySegment::where('slug', $slug) # check slug existence 
                ->where('id', '!=', $communitySegment->id) # ensure not to compare slug to itself 
                ->exists()
            ) {
                $randomId = Str::lower(Str::random(8));
                $slug = "{$slug}-{$randomId}";
            }
            $communitySegment->slug = $slug; # final base value 
        }
    }

    /**
     * Handle the CommunitySegment "updated" event.
     */
    public function updated(CommunitySegment $communitySegment): void
    {
        //
    }

    /**
     * Handle the CommunitySegment "deleted" event.
     */
    public function deleted(CommunitySegment $communitySegment): void
    {
        //
    }

    /**
     * Handle the CommunitySegment "restored" event.
     */
    public function restored(CommunitySegment $communitySegment): void
    {
        //
    }

    /**
     * Handle the CommunitySegment "force deleted" event.
     */
    public function forceDeleted(CommunitySegment $communitySegment): void
    {
        //
    }
}
