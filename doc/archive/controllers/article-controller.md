# Unused codes in the ArticleController


## restore()
### 1.0: simple restoration
    public function restore($id)
    {
        $article = Article::findOrFail($id);
        $article->restoreFromArchive(); # calls the trait method 

        return response()->json([
            'message' => 'Article has been unarchive'
        ]);
    }

## update()
### 1.1: individual isset check for fields
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
### 1.2: with logs for debugging
   public function update(UpdateArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        $validatedArticle = $request->validated();
        $storage = Storage::disk('public');


        // Handler 1: cover photo upload or URL
        if ($request->hasFile('cover_photo')) {

            # delete old cover if it exists
            if ($article->cover_photo && $storage->exists($article->cover_photo)) {
                $storage->delete($article->cover_photo);
            }

            $validatedArticle['cover_photo'] = $request->file('cover_photo')->store('articles/covers', 'public');
        } else {
            // Exclude cover in any subsequent db operation
            unset($validatedArticle['cover_photo']);
        }

        /**
         * Handler 2: thumbnail_same_as_cover logic 
         * when thumbnail is the same as cover, copy all cover properties to thumbnail
         */
        if ($request->has('thumbnail_same_as_cover') && $request->thumbnail_same_as_cover) {

            # if same as cover, force thumbnail with adapt cover_photo  
            $validatedArticle['thumbnail'] = $validatedArticle['cover_photo'] ?? $article->cover_photo;

            # force adapt cover artist 
            if (!$request->has('thumbnail_artist_id')) {
                $validatedArticle['thumbnail_artist_id'] = $validatedArticle['cover_artist_id'] ?? $article->cover_artist_id;
            }
        } else {

            // Handler 3: thumbnail upload (only if not using same as cover)
            if ($request->hasFile('thumbnail')) {

                # delete old thumbnail if it exists and it's not same as cover
                if ($article->thumbnail && $storage->exists($article->thumbnail)) {
                    $storage->delete($article->thumbnail);
                }

                $validatedArticle['thumbnail'] = $request->file('thumbnail')->store('articles/covers', 'public');
            } else {
                // Exclude cover in any subsequent db operation
                unset($validatedArticle['thumbnail']);
            }
        }

        Log::info('Request method: ' . $request->method());
        Log::info('Request data: ', $request->all());
        Log::info('Files: ', $request->allFiles());
        Log::info('Has cover_photo file: ' . ($request->hasFile('cover_photo') ? 'yes' : 'no'));
        Log::info('Has thumbnail file: ' . ($request->hasFile('thumbnail') ? 'yes' : 'no'));

        $article->update($validatedArticle);
        // reload relationships
        $article->load(['category', 'writer', 'coverArtist', 'thumbnailArtist']);
        return ArticleResource::make($article);
    }


## destroy() 
### 1.0: first version
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        // Delete associated files before deleting article 
        if ($article->cover_photo && Storage::disk('public')->exists($article->cover_photo)) {
            Storage::disk('public')->delete($article->cover_photo);
        }

        if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
            Storage::disk('public')->delete($article->thumbnail);
        }

        $article->delete();
        return response()->json(['message' => 'Article deleted successfully'], 200);
    }
### 1.1: debugging
   public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        $storage = Storage::disk('public');

        $trashDir = 'articles/trash/';

        Log::info('Article cover_photo' . ($article->cover_photo ?? 'NULL'));
        Log::info("Article thumbnail: " . ($article->thumbnail ?? 'NULL'));

        // Check if trash directory exists 
        if (!$storage->exists($trashDir)) {
            $storage->makeDirectory($trashDir);
            Log::info('Created trash directory');
        }

        // Delete associated files before deleting article 
        if ($article->cover_photo && $storage->exists($article->cover_photo)) {
            Log::info("Cover photo exists, attempting to move");
            $filename = basename($article->cover_photo);
            $moveResult = $storage->move($article->cover_photo, $trashDir . $filename);

            $trashPath = $trashDir . $filename;

            Log::info("Move result: " . ($moveResult ? 'SUCCESS' : 'FAILED'));
            Log::info("File now exists at destination: " . (Storage::disk('public')->exists($trashPath) ? 'YES' : 'NO'));
        } else {
            Log::info("Cover photo does not exist or is null");
            if ($article->cover_photo) {
                Log::info("File path was: " . $article->cover_photo);
                Log::info("File exists check: " . (Storage::disk('public')->exists($article->cover_photo) ? 'YES' : 'NO'));
            }
        }

        if ($article->thumbnail && $storage->exists($article->thumbnail)) {
            $filename = basename($article->thumbnail);
            $storage->move($article->thumbnail, $trashDir . $filename);
        }

        $article->delete();
        return response()->json(['message' => 'Article deleted successfully'], 200);
    }

## restore()
### 1.0: with helper

       public function restore(Article $article)
    {
        $storage = Storage::disk('public');
        $trashDir = 'articles/trash/';

        // Anonymous function helper 
        $restoreFile = function ($filePath) use ($storage, $trashDir) {
            if (!$filePath) return; # if null, empty or falsy it will exit

            $filename = basename($filePath);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $filePath);
            }
        };

        $restoreFile($article->cover_photo);
        $restoreFile($article->thumbnail);

        $article->restore();

        return response()->json([
            'message' => 'Article was restored',
            'data' => ArticleResource::make($article)
        ]);
    }

































