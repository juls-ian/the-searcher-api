# Scrapped codes in the Archivable trait 

## v.1: archive() - still stores archivable_type into uppercase | bypassing the morph map in the AppServiceProvider
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

        $archiveType = get_class($this);

        $archive = Archive::create([
            'archivable_type' => class_basename($archiveType),
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