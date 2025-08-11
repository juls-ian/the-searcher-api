<?php

namespace App\Observers;

use App\Models\Issue;
use Illuminate\Support\Str;

class IssueObserver
{
    /**
     * Handle the Issue "created" event.
     */
    public function created(Issue $issue): void
    {
        //
    }

    public function creating(Issue $issue)
    {
        $baseSlug = Str::slug($issue->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        while (Issue::where('slug', $slug)->exists()) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}";
        }
        $issue->slug = $slug; # final base value 

    }

    public function updating(Issue $issue)
    {
        if ($issue->isDirty('title')) {

            $baseSlug = Str::slug($issue->title);
            $slugDate = now()->format('Y-m-d');
            $slug = "{$baseSlug}-{$slugDate}";

            // Slug existence check 
            while (
                Issue::where('slug', $slug)
                ->where('id', '!=', $issue->id)
                ->exists()
            ) {
                $randomId = Str::lower(Str::random(8));
                $slug = "{$slug}-{$randomId}";
            }
            $issue->slug = $slug; # final base
        }
    }

    /**
     * Handle the Issue "updated" event.
     */
    public function updated(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "deleted" event.
     */
    public function deleted(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "restored" event.
     */
    public function restored(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "force deleted" event.
     */
    public function forceDeleted(Issue $issue): void
    {
        //
    }
}
