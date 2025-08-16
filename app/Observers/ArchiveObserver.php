<?php

namespace App\Observers;

use App\Models\Archive;
use App\Models\Article;
use Illuminate\Support\Str;

class ArchiveObserver
{
    /**
     * Handle the Archive "created" event.
     */
    public function created(Archive $archive): void
    {
        //
    }

    public function creating(Archive $archive)
    {
        $baseSlug = Str::slug($archive->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        while (Archive::where('slug', $slug)->exists()) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}";
        }
        $archive->slug = $slug;
    }

    public function updating(Archive $archive)
    {
        $baseSlug = Str::slug($archive->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        while (
            Article::where('slug', $slug)
            ->where('id', '!=', $archive->id) # prevent comparing slug to itself 
            ->exists()
        ) {
            $randomId = Str::lower(Str::random(8));
            $slug = "{$slug}-{$randomId}";
        }
        $archive->slug = $slug;
    }

    /**
     * Handle the Archive "updated" event.
     */
    public function updated(Archive $archive): void
    {
        //
    }

    /**
     * Handle the Archive "deleted" event.
     */
    public function deleted(Archive $archive): void
    {
        //
    }

    /**
     * Handle the Archive "restored" event.
     */
    public function restored(Archive $archive): void
    {
        //
    }

    /**
     * Handle the Archive "force deleted" event.
     */
    public function forceDeleted(Archive $archive): void
    {
        //
    }
}
