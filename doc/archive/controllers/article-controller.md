# Unused codes in the ArticleController


## v.1: restore()
    public function restore($id)
    {
        $article = Article::findOrFail($id);
        $article->restoreFromArchive(); # calls the trait method 

        return response()->json([
            'message' => 'Article has been unarchive'
        ]);
    }

## v.2: update()
 public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $validatedArchive = $request->validated();
        $archivableData = $validatedArchive['data'] ?? null;

        $oldData = $archive->data ?? []; # get old data to delete old files 

        if (is_array($archivableData)) {
            foreach ($archivableData as $key => $value) {
                if ($request->hasFile("data.$key")) {
                    $file = $request->file("data.$key"); # get uploaded file 
                    $mimeType = $file->getMimeType(); # get mime type 

                    if (str_starts_with($mimeType, 'image/')) {
                        $path = $request->file("data.$key")->store('archives/covers', 'public'); # store covers
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $path = $request->file("data.$key")->store('archives/videos', 'public'); # store videos 
                    } else {
                        $path = $request->file("data.$key")->store('archives/files', 'public');
                    }

                    $archivableData[$key] = $path; #replace original value with URL or storage path 
                }
            }
        }

        // Delete old files being replaced 
        if (is_array($oldData)) {
            foreach ($oldData as $key => $value) {
                if (is_string($value) && str_starts_with($value, 'archives/')) {
                    Storage::disk('public')->delete($value);
                }
            }
        }

        $updateData = []; # batch of data 

        // Update data 
        if (isset($validatedArchive['title'])) {
            $updateData['title'] = $validatedArchive['title'];
        }

        if (isset($validatedArchive['archivable_type'])) {
            $updateData['archivable_type'] = $validatedArchive['archivable_type'];
        }

        if (isset($validatedArchive['archivable_id'])) {
            $updateData['archivable_id'] = $validatedArchive['archivable_id'];
        }

        if (isset($validatedArchive['archiver_id'])) {
            $updateData['archiver_id'] = $validatedArchive['archiver_id'];
        }

        // Always update data if provided, otherwise keep existing data
        if (isset($validatedArchive['data'])) {
            $updateData['data'] = $archivableData;
        }

        $archive->update($updateData);

        return response()->json([
            'message' => 'Archive successfully updated',
            'data' => new ArchiveResource($archive->fresh())
        ]);
    }