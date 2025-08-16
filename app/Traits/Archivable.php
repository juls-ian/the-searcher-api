<?php

namespace App\Traits;

use App\Models\Archive;
use Illuminate\Support\Facades\Auth;

/**
 * Any model using this trait can be archived 
 */

trait Archivable
{
    // Relationship to the Archives 
    public function archives()
    {
        return $this->morphMany(Archive::class, 'archivable')->withTrashed();
    }

    public function archive()
    {
        // Check 1: if model already has archived_at
        if ($this->archived_at) {
            return; # already archived, no new row
        }

        // Check 2: actual archive record - archive existence check 
        $exists = $this->archives()
            ->withTrashed() # softDeleted 
            ->where('archivable_id', $this->id)
            ->where('archivable_type', get_class($this))
            ->exists();

        // Prevent duplicate snapshots
        if ($exists) {
            return;
        }



        $archive =  $this->archives()->create([ # use archives() relationship 
            'archivable_id' => $this->id,
            'title' => $this->title ?? null,
            'slug' => $this->slug ?? null,
            'data' => $this->toArray(),
            'archiver_id' => Auth::id(),
            'archived_at' => now()
        ]);

        $this->update(['archived_at' => now()]);

        return $archive;
    }

    public function restoreFromArchive()
    {
        $this->update(['archived_at' => null]);
    }

    public function scopeArchived($query)
    {
        return $this->whereNotNull('archived_at');
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}