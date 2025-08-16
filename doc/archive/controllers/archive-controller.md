# Unused codes in the ArchiveController


## v.1: bulk actions
    /**
     * Bulk restore multiple archives.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array',
            'archive_ids.*' => 'integer|exists:archives,id'
        ]);

        $archives = Archive::onlyTrashed()->whereIn('id', $request->archive_ids)->get();
        
        foreach ($archives as $archive) {
            $archive->restore();
            $archive->archivable()->update(['archived_at' => now()]);
        }

        return response()->json([
            'message' => count($archives) . ' archives restored successfully'
        ]);
    }

    /**
     * Bulk hard delete multiple archives.
     */
    public function bulkForceDestroy(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array',
            'archive_ids.*' => 'integer|exists:archives,id'
        ]);

        $count = Archive::withTrashed()->whereIn('id', $request->archive_ids)->forceDelete();

        return response()->json([
            'message' => $count . ' archives permanently deleted'
        ]);
    }

## v.2: restore() - simple restore function 
    public function restore($id)
    {
        // Find soft deleted entries
        $archive = Archive::onlyTrashed()->findOrFail($id);

        // Restore archive 
        $archive->restore();

        // Re-set archived_at timestamp in the related model 
        $archive->archivable()->update(['archived_at' => now()]);

        return response()->json([
            'message' => 'Archive was restored',
            'data' => ArchiveResource::make($archive->load('archivable'))
    }


## v.3: destroy() - doesn't save the original dirname
    public function destroy(Archive $archive)
    {
        // Convert to array 
        $data = is_string($archive->data)
            # (assumes string contains JSON), convert to assoc array and return array instead of object 
            ? json_decode($archive->data, true)
            : $archive->data;  # if data has value it uses that value 

        $this->processFiles($data, function ($path) use (&$data) {
            $originalPath = ltrim($path, '/'); # remove the leading "/"
            $newPath = 'archives/trash/' . basename($path);

            $storage = Storage::disk('public');
            $storage->makeDirectory('archives/trash');

            if ($storage->exists($originalPath)) {
                $storage->move($originalPath, $newPath);
            }

            // Replace path in data json 
            array_walk_recursive($data, function (&$item) use ($path, $newPath) {
                if ($item === $path) {
                    $item = $newPath;
                }
            });
        });

        // Save updated paths pointing to trash 
        $archive->update(['data' => $data]);

        // Soft delete the archive
        $archive->delete();
        return response()->json([
            'message' => 'Archive deleted successfully'
        ]);
    }

## v.3.1: destroy() - not working
    public function destroy(Archive $archive)
    {
        // Convert to array 
        $data = is_string($archive->data)
            # (assumes string contains JSON), convert to assoc array and return array instead of object 
            ? json_decode($archive->data, true)
            : $archive->data;  # if data has value it uses that value 


        $storage = Storage::disk('public');
        $storage->makeDirectory('archives/trash');

        // Replace path in data json 
        array_walk_recursive($data, function (&$item, $key) use ($storage) {

            if (is_array($item) && isset($item['path'], $item['original_dir'])) {
                $currentPath = $item['path'];
                $newPath = 'archives/trash/' . basename($currentPath);

                if ($storage->exists($currentPath)) {
                    $storage->move($currentPath, $newPath);
                }

                // Update path to trash location 
                $item['path'] = $newPath;
                # keep original dir for future restore 
            }
        });

        // Save updated paths pointing to trash 
        $archive->update(['data' => $data]);

        // Soft delete the archive
        $archive->delete();
        return response()->json([
            'message' => 'Archive deleted successfully'
        ]);
    }


## v.4: store()
    public function store(StoreArchiveRequest $request)
    {
        //
        $validatedArchive = $request->validated();

        $archivableData = $validatedArchive['data'] ?? [];

        #                         'cover' => null 
        foreach ($archivableData as $key => $value) {
            if ($request->hasFile("data.$key")) {

                $file = $request->file("data.$key"); # get uploaded file 
                $mimeType = $file->getMimeType(); # get file type: returns "image/jpg"

                // Store uploads based on type 
                if (str_starts_with($mimeType, 'image/')) {
                    # store covers
                    $path = $request->file("data.$key")->store('archives/covers', 'public');
                } elseif (str_starts_with($mimeType, 'video/')) {
                    # store videos 
                    $path = $request->file("data.$key")->store('archives/videos', 'public');
                } else {
                    # store files 
                    $path = $request->file("data.$key")->store('archives/files', 'public');
                }

                // Replace original value with URL or storage path 
                $archivableData[$key] = [
                    'path' => $path,
                    'original_dir' => dirname($path)
                ];
            }
        }

        $archive = Archive::create([
            'archivable_type' => $validatedArchive['archivable_type'],
            'archivable_id' => $validatedArchive['archivable_id'] ?? null,
            'title' => $validatedArchive['title'],
            'data' => json_encode($archivableData),
            'archived_at' => now(),
            'archiver_id' => $validatedArchive['archiver_id']

        ]);

        return response()->json([
            'message' => 'Archive created successfully',
            'data' => new ArchiveResource($archive)
        ]);
    }


## v.5: update() - brittle 
    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $validatedArchive = $request->validated();
        $archivableData = $validatedArchive['data'] ?? null; # raw validated data 

        // Convert to array 
        $oldData = is_string($archive->data)
            # if string (contains JSON), convert to assoc array and return array instead of object 
            ? json_decode($archive->data, true)
            # if data has value it uses that value hence = empty array 
            : ($archive->data ?? []);

        if (is_array($archivableData)) {
            foreach ($archivableData as $key => $value) {
                // Checks if a file was uploaded for this specific field (like data.cover_image or data.video)
                if ($request->hasFile("data.$key")) {
                    # delete old file only if it exists for this key/field 
                    if (isset($oldData[$key]) && str_starts_with($oldData[$key], 'archives/')) {
                        Storage::disk('public')->delete($oldData[$key]);
                    }

                    $file = $request->file("data.$key"); # get uploaded file 
                    $mimeType = $file->getMimeType(); # get mime type: returns 'image/jpeg', 'video/mp4', 'application/pdf'

                    // Handle different file types storage 
                    if (str_starts_with($mimeType, 'image/')) {
                        $path = $request->file("data.$key")->store('archives/covers', 'public'); # store covers
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $path = $request->file("data.$key")->store('archives/videos', 'public'); # store videos 
                    } else {
                        $path = $request->file("data.$key")->store('archives/files', 'public');
                    }

                    $archivableData[$key] = [
                        'path' => $path, #replace original value with URL or storage path 
                    ];
                } else {
                    // if no new file, keep the old files 
                    $archivableData[$key] = $oldData[$key] ?? $value;
                }
            }
        }

        $updateData = []; # batch of updated data 

        // Field whitelist
        foreach (['title', 'archivable_type', 'archivable_id', 'archiver_id'] as $field) {
            # if field exists in $validatedArchive it copies to the $updateData
            if (isset($validatedArchive[$field])) {
                $updateData[$field] = $validatedArchive[$field];
            }
        }

        // Check if data field is submitted 
        if (isset($validatedArchive['data'])) {
            $updateData['data'] = $archivableData; # fully processed data ready for storage 
        }

        $archive->update($updateData);

        return response()->json([
            'message' => 'Archive successfully updated',
            'data' => new ArchiveResource($archive->fresh())
        ]);
    }

## v.6: update() - another version
    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $validatedArchive = $request->validated();
        $archivableData = $validatedArchive['data'] ?? null; # raw validated data 

        // Convert to array 
        $oldData = is_string($archive->data)
            # if string (contains JSON), convert to assoc array and return array instead of object 
            ? json_decode($archive->data, true)
            # if data has value it uses that value hence = empty array 
            : ($archive->data ?? []);

        if (is_array($archivableData)) {
            foreach ($archivableData as $key => $value) {
                // Checks if a file was uploaded for this specific field (like data.cover_image or data.video)
                if ($request->hasFile("data.$key")) {

                    $file = $request->file("data.$key"); # get uploaded file 
                    $mimeType = $file->getMimeType(); # get mime type: returns 'image/jpeg', 'video/mp4', 'application/pdf'

                    // Handle different file types storage 
                    if (str_starts_with($mimeType, 'image/')) {
                        $dir = 'archives/covers';
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $dir = 'archives/videos';
                    } else {
                        $dir = 'archives/files';
                    }

                    // Store files first 
                    $path = $file->store($dir, 'public');

                    $archivableData[$key] = [
                        'path' => $path, #replace original value with URL or storage path 
                        'original_dir' => $dir # save the original directory
                    ];


                    # delete old file only if it exists for this key/field 
                    if (isset($oldData[$key]) && str_starts_with($oldData[$key], 'archives/')) {
                        Storage::disk('public')->delete($oldData[$key]);
                    }
                } else {
                    // if no new file, keep the old files 
                    $archivableData[$key] = $oldData[$key] ?? $value;
                }
            }
        }

        $updateData = []; # batch of updated data 

        // Field whitelist
        foreach (['title', 'archivable_type', 'archivable_id', 'archiver_id'] as $field) {
            # if field exists in $validatedArchive it copies to the $updateData
            if (isset($validatedArchive[$field])) {
                $updateData[$field] = $validatedArchive[$field];
            }
        }

        // Check if data field is submitted 
        if (isset($validatedArchive['data'])) {
            $updateData['data'] = $archivableData; # fully processed data ready for storage 
        }

        $archive->update($updateData);

        return response()->json([
            'message' => 'Archive successfully updated',
            'data' => new ArchiveResource($archive->fresh())
        ]);
    }


## v.7: restore() - simple
    public function restore($id)
    {
        // Find soft deleted entries
        $archive = Archive::onlyTrashed()->findOrFail($id);

        $data = is_string($archive->data)
            ? json_decode($archive->date, true)
            : $archive->data;

        $this->processFiles($data, function ($path) use (&$data) {
            $storage = Storage::disk('public');
            $originalPath = ltrim($path, '/');

            // If file is inside trash, move it back 
            if (str_starts_with($originalPath, 'archives/path')) {
            }
        });

        // Restore archive 
        $archive->restore();

        // Re-set archived_at timestamp in the related model 
        $archive->archivable()->update(['archived_at' => now()]);

        return response()->json([
            'message' => 'Archive was restored',
            'data' => ArchiveResource::make($archive->load('archivable'))
        ]);
    }