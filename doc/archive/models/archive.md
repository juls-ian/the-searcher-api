# Scrapped codes from Archive model 

## boot()
### 1.0: overriding the deleting and forceDeleting

    protected static function boot()
    {
        parent::boot();

        // Move soft deleted to trash 
        static::deleting(function ($archive) {
            if (!$archive->isForceDeleting()) {
                $archive->moveFilesToTrash();
            }
        });

        // Delete files on force delete
        static::forceDeleting(function ($archive) {
            $archive->deleteFiles();
        });
    }

### booted()
### 1.0: automatically setting the archiver_id
    public static function booted()
    {
        static::creating(function ($archive) {
            if (!$archive->archiver_id && Auth::id()) {
                $archive->archiver_id = Auth::id();
            }
        });
    }

## deleteFiles()
### 1.0: initial code
public function deleteFiles()
    {
        $data = $this->data ?? [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value) && str_starts_with($value, 'archives/')) {
                    if (Storage::disk('public')->exists($value)) {
                        Storage::disk('public')->delete($value);
                    }
                }
            }
        }
    }

## moveFilesToTrash()
### 1.0: initial code
    public function moveFilesToTrash()
    {
        $data = $this->data ?? [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value) && str_starts_with($value, 'archives/')) {
                    $originalPath = ltrim($value, '/');
                    $newPath = 'archives/trash/' . basename($value);

                    Storage::disk('public')->makeDirectory('archives/trash');

                    if (Storage::disk('public')->exists($originalPath)) {
                        Storage::disk('public')->move($originalPath, $newPath);
                        $data[$key] = $newPath;
                    }
                }
            }
        }

        $this->data = $data;
        $this->saveQuietly();
    }