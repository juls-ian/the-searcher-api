# Unused codes in the MultimediaController 

## store()
### 1.0: initial code
public function store(StoreMultimediaRequest $request)
    {
        $validatedMultimedia = $request->validated();

        // Handler 1: files upload 
        if ($request->hasFile('files')) {
            $filesPaths = []; # array to store the file paths 

            foreach ($request->file('files') as $file) {
                $fileName = $file->store('multimedia/files', 'public');
                $filesPaths[] = $fileName;
            }
            $validatedMultimedia['files'] = json_encode($filesPaths);
        } else {
            $validatedMultimedia['files'] = json_encode([]);
        }

        // Handler 2: thumbnail upload 
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('multimedia/thumbnails', 'public');
            $validatedMultimedia['thumbnail'] = $thumbnailPath;
        }

        // Handler 3: published_at date/time
        if (isset($validatedMultimedia['published_at']) && $validatedMultimedia['published_at']) {
            # if date is set, convert it to Carbon instance 
            $validatedMultimedia['published_at'] = Carbon::parse($validatedMultimedia['published_at']);
        } else {
            $validatedMultimedia['published_at'] = Carbon::now();
        }

        $multimedia = Multimedia::create($validatedMultimedia);
        // Eager load relationships to User 
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }
### 1.1: when log for debugging
public function store(StoreMultimediaRequest $request)
    {

        $validatedMultimedia = $request->validated();

        // Debug: Check what we're receiving
        Log::info('Has files:', ['hasFile' => $request->hasFile('files')]);
        Log::info('Files received:', ['files' => $request->file('files')]);
        Log::info('All files:', ['allFiles' => $request->allFiles()]);

        // Handler 1: files upload 
        if ($request->hasFile('files')) {
            $filesPaths = [];
            $files = $request->file('files');

            Log::info('Files type:', ['is_array' => is_array($files), 'type' => gettype($files)]);

            // Convert to array if needed
            if (!is_array($files)) {
                $files = [$files];
            }

            Log::info('Files after conversion:', ['count' => count($files)]);

            foreach ($files as $index => $file) {
                Log::info("Processing file {$index}:", [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'valid' => $file->isValid()
                ]);

                $filesPath = $file->store('multimedia/files', 'public');
                $filesPaths[] = $filesPath;

                Log::info("File {$index} stored at:", ['path' => $filesPath]);
            }

            $validatedMultimedia['files'] = json_encode($filesPaths);
            Log::info('Final files value:', ['files' => $validatedMultimedia['files']]);
        } else {
            Log::info('No files detected in request');
        }

        // Rest of your code...
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('multimedia/thumbnails', 'public');
            $validatedMultimedia['thumbnail'] = $thumbnailPath;
        }

        if (isset($validatedMultimedia['published_at']) && $validatedMultimedia['published_at']) {
            $validatedMultimedia['published_at'] = Carbon::parse($validatedMultimedia['published_at']);
        } else {
            $validatedMultimedia['published_at'] = Carbon::now();
        }

        $multimedia = Multimedia::create($validatedMultimedia);
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }
### 1.2: with array slice 
    public function store(StoreMultimediaRequest $request)
    {

        $validatedMultimedia = $request->validated();

        // Handler 1: files upload 
        if ($request->hasFile('files')) {
            $filesPaths = []; # array to store the file paths 
            $files = $request->file('files');

            # convert non-array files into array if needed 
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                $filePath = $file->store('multimedia/files', 'public');
                $filesPaths[] = $filePath;
            }

            $validatedMultimedia['files'] = json_encode($filesPaths);
        } else {
            $validatedMultimedia['files'] = json_encode([]);
        }

        // Handler 2: thumbnail upload 
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('multimedia/thumbnails', 'public');
            $validatedMultimedia['thumbnail'] = $thumbnailPath;
        }

        // Handler 3: published_at date/time
        if (isset($validatedMultimedia['published_at']) && $validatedMultimedia['published_at']) {
            # if date is set, convert it to Carbon instance 
            $validatedMultimedia['published_at'] = Carbon::parse($validatedMultimedia['published_at']);
        } else {
            $validatedMultimedia['published_at'] = Carbon::now();
        }

        // Handler 4: multiple multimedia_artists 
        $artistIds = $validatedMultimedia['multimedia_artists_id'] ?? [];

        if (empty($artistIds)) {
            return response()->json(['error' => 'At least one artist is required'], 422);
        }

        $validatedMultimedia['multimedia_artists_id'] = $artistIds[0]; # set first as primary 
        $additionalArtists = array_slice($artistIds, 1);

        $multimedia = Multimedia::create($validatedMultimedia);

        # attach additional artists 
        if (!empty($additionalArtists)) {
            $multimedia->additionalArtists()->attach($additionalArtists);
        }

        // Eager load relationships to User 
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }

## update()
### 1.1: other version
   public function update(UpdateMultimediaRequest $request, Multimedia $multimedia)
    {
        $validatedMultimedia = $request->validated();
        $storage = Storage::disk('public');

        // Handler 1: files upload (multiple files as JSON array)
        if ($request->hasFile('files')) {

            # delete old cover if it exists 
            $oldFiles = json_decode($multimedia->files, true);
            if (is_array($oldFiles)) {
                foreach ($oldFiles as $oldFile) {
                    if ($storage->exists($oldFile)) {
                        $storage->delete($oldFile);
                    }
                }
            }

            $filesPaths = [];
            $files = $request->file('files');

            // Convert non-array files into array if needed 
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $filePath = $file->store('multimedia/files', 'public');
                $filesPaths[] = $filePath;
            }

            $validatedMultimedia['files'] = json_encode($filesPaths);
        } elseif (isset($validatedMultimedia['files'])) {
            // Handle URL strings or array of URLs 
            $filesURL = $validatedMultimedia['files'];

            if (is_string($filesURL)) {
                $filesURL = [$filesURL];
            }

            // Store as JSON array 
            $validatedMultimedia['files'] = json_encode($filesURL);
        } else {
            // Exclude cover in any subsequent db operation 
            unset($validatedMultimedia['files']);
        }


        // Handler 2: thumbnail upload 
        if ($request->hasFile('thumbnail')) {

            # delete old thumbnails if it exists 
            if ($multimedia->thumbnail && $storage->exists($multimedia->thumbnail)) {
                $storage->delete($multimedia->thumbnail);
            }
            $validatedMultimedia['thumbnail'] = $request->file('thumbnail')->store('multimedia/thumbnails');
        } else {
            unset($validatedMultimedia['thumbnail']);
        }

        // Handler 3: multiple multimedia_artists 
        if (array_key_exists('multimedia_artists_id', $validatedMultimedia)) {
            $artistIds = $validatedMultimedia['multimedia_artists_id'];
            unset($validatedMultimedia['multimedia_artists_id']); # remove from main data

            // Update artist relationships only when explicitly provided 
            if (!empty($artistIds)) {
                $multimedia->multimediaArtists()->sync($artistIds); # sync = replace all ids with new ones 
            }
        }

        $multimedia->update($validatedMultimedia);
        # reload relationships 
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }

## destroy()
### 1.0: with try and catch 
   public function destroy(Multimedia $multimedia)
    {
        $this->authorize('delete', $multimedia);

        $storage = Storage::disk('public');

        // Delete files stored as JSON array 
        if ($multimedia->files) {
            $files = json_decode($multimedia->files, true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    # deletes the local files 
                    if ($storage->exists($file)) {
                        $storage->delete($file);
                    }
                }
            }
        }

        if ($multimedia->thumbnail && $storage->exists($multimedia->thumbnail)) {
            $storage->delete($multimedia->thumbnail);
        }

        try {
            $multimedia->delete();
            return response()->json(['message' => 'Multimedia deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete multimedia'], 500);
        }
    }
### 1.1: not implementing soft delete
    public function destroy(Multimedia $multimedia)
    {
        $this->authorize('delete', $multimedia);
        $storage = Storage::disk('public');

        // Delete files stored as JSON array 
        if ($multimedia->files) {
            $files = json_decode($multimedia->files, true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    # deletes the local files 
                    if ($storage->exists($file)) {
                        $storage->delete($file);
                    }
                }
            }
        }

        if ($multimedia->thumbnail && $storage->exists($multimedia->thumbnail)) {
            $storage->delete($multimedia->thumbnail);
        }

        try {
            $multimedia->delete();
            return response()->json(['message' => 'Multimedia deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete multimedia'], 500);
        }
    }

## showArchived()
### 1.0: initial version
   public function showArchived($id)
    {
        $archive = Archive::findOrFail($id);
        return response()->json($archive);
    }
### 1.1: before improvement
    public function showArchived($id)
    {

        try {
            $archive = Archive::where('archivable_type', 'multimedia')
                ->where('id', $id)
                ->firstOrFail();
            return response()->json($archive);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Can show only archived multimedia']);
        }
    }

## archiveIndex()
### 1.0: initial version
 public function archiveIndex()
    {
        $archivedMultimedia = Multimedia::archived()->get(); # query scope
        return response()->json($archivedMultimedia);
    }

    

    






