# Scrapped codes from Archive model 

## v.1:boot()

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

## v.2: deleteFiles()
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

## v.3: moveFilesToTrash()


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