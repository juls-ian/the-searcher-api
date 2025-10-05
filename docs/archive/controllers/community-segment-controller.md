# Unused codes in the StoreCommunitySegmentController


## store()
### 1.0: initial 
    public function store(StoreCommunitySegmentRequest $request)
    {
        $segment = CommunitySegment::create($request->validated());

        if ($segment->segment_type === 'article') {
            SegmentsArticle::create([
                'segment_id' => $segment->id,
                'body' => $request->body
            ]);
        } else {
            SegmentsPoll::create([
                'segment_id' => $segment->id,
                'question' => $request->question,
                'options' => $request->options,
                'ends_at' => $request->ends_at
            ]);
        }

        return response()->json($segment);
    }

## update()
### 1.0: causing an error during update
    public function update(UpdateCommunitySegmentRequest $request, CommunitySegment $communitySegment)
    {
        $this->authorize('update', $communitySegment);

        $validatedSegment = $request->validated();
        $storage = Storage::disk('public');

        // Handler 1: segment_cover 
        if ($request->hasFile('segment_cover')) {

            # delete old cover if it exists
            if ($communitySegment->segment_cover && $storage->exists($communitySegment->segment_cover)) {
                $storage->delete($communitySegment->segment_cover);
            }

            $validatedSegment['segment_cover'] = $request->file('segment_cover')->store('community-segments/covers', 'public');
        } else {
            // Cover won't be included in any subsequent db operation
            unset($validatedSegment['segment_cover']);
        }

        // Handler 3: manage series_order based on series_type changes
        if (isset($validatedSegment['series_type'])) {
            if ($validatedSegment['series_type'] === 'series_header') {
                $validatedSegment['series_order'] = 1;
            } elseif ($validatedSegment['series_type'] === 'standalone') {
                $validatedSegment['series_order'] = null;
            }
        }
        $communitySegment->update($validatedSegment);

        if ($communitySegment->segment_type === 'article') {
            // Access poll data from article_segments key 
            $articleData = $request->input('article_segments'); # for nested data, key is the object key
            SegmentsArticle::where('segment_id', $communitySegment->id)->update([
                'body' => $articleData['body'] ?? null
            ]);
        } else if ($communitySegment->segment_type === 'poll') {
            // Accessing poll data from poll_segments key 
            $pollData = $request->input('poll_segments'); # input() for nested data 

            SegmentsPoll::where('segment_id', $communitySegment->id)->update([
                'question' => $pollData['question'] ?? null,
                'options' => $pollData['options'] ?? null,
                'ends_at' => $pollData['ends_at'] ?? null
            ]);
        }


        # reload relationships
        $communitySegment->load(['writer', 'coverArtist', 'series', 'segmentPolls', 'segmentArticles']);
        return CommunitySegmentResource::make($communitySegment);
    }

## destroy()
### 1.0: initial code 
    public function destroy(CommunitySegment $communitySegment)
    {
        $this->authorize('delete', $communitySegment);

        $storage = Storage::disk('public');
        if ($communitySegment->segment_cover && $storage->exists($communitySegment->segment_cover)) {
            $storage->delete($communitySegment->segment_cover);
        }

        $communitySegment->delete();
        return response()->json(['message' => 'Segment deleted successfully']);
    }
### 1.1: removed saveQuietly
    public function destroy(CommunitySegment $communitySegment)
    {
        $this->authorize('delete', $communitySegment);
        $storage = Storage::disk('public');
        $trashDir = 'community-segments/trash/';

        if (!$trashDir) {
            $storage->makeDirectory($trashDir);
        }

        if ($communitySegment->segment_cover && $storage->exists($communitySegment->segment_cover)) {
            $filename = basename($communitySegment->segment_cover);
            $filePath = $trashDir . $filename;
            $storage->move($communitySegment->segment_cover, $filePath);

            $communitySegment->segment_cover = $filePath;
            $communitySegment->saveQuietly();
        }

        $communitySegment->delete();
        return response()->json(['message' => 'Segment deleted successfully']);
    }

## restore()
### 1.0: initial code, $communitySegment->segment_cover initial starting dir is the trash 
    public function restore(CommunitySegment $communitySegment)
    {
        $storage = Storage::disk('public');
        $trashDir = 'community-segments/trash/';

        Log::info(['cover_photo' => $communitySegment->segment_cover]);


        if ($communitySegment->segment_cover) {
            $filename = basename($communitySegment->segment_cover);
            $trashPath = $trashDir . $filename;

            Log::info(['filename' => $filename]);
            Log::info(['trash path' => $trashPath]);

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $communitySegment->segment_cover);
            }
        }

        $communitySegment->restore();
        return response()->json([
            'message' => 'Community segment restored',
            'data' => CommunitySegmentResource::make($communitySegment)
        ]);
    }
### 1.1: refined code
public function restore(CommunitySegment $communitySegment)
{
    $storage = Storage::disk('public');
    $trashDir = 'community-segments/trash/';
    $baseDir  = 'community-segments/'; // original folder

    if ($communitySegment->segment_cover) {
        $filename = basename($communitySegment->segment_cover);
        $trashPath = $trashDir . $filename;
        $restorePath = $baseDir . $filename;

        Log::info(['filename' => $filename]);
        Log::info(['trash path' => $trashPath]);
        Log::info(['restore path' => $restorePath]);

        if ($storage->exists($trashPath)) {
            $storage->move($trashPath, $restorePath);

            // update DB column back to original path
            $communitySegment->segment_cover = $restorePath;
        }
    }

    $communitySegment->restore();
    $communitySegment->save();

    return response()->json([
        'message' => 'Community segment restored',
        'data' => CommunitySegmentResource::make($communitySegment)
    ]);
}
### 1.2: removed $baseDir
  public function restore(CommunitySegment $communitySegment)
    {
        $this->authorize('restore', $communitySegment);
        $storage = Storage::disk('public');
        $trashDir = 'community-segments/trash/';
        $baseDir  = 'community-segments/covers/'; # original folder


        if ($communitySegment->segment_cover) {
            $filename = basename($communitySegment->segment_cover);
            $trashPath = $trashDir . $filename;
            $restorePath = $baseDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $restorePath);
                // Set back the db column to its original path 
                $communitySegment->segment_cover = $restorePath;
            }
        }

        $communitySegment->restore();
        $communitySegment->save();

        return response()->json([
            'message' => 'Community segment restored',
            'data' => CommunitySegmentResource::make($communitySegment)
        ]);
    }

## archivedIndex()
### 1.0: initial code
    public function archiveIndex()
    {
        $archivedSegments = CommunitySegment::archived()->get(); # uses trait scope
        return response()->json($archivedSegments);
    }

## showArchived()
### 1.0: initial code
    public function showArchived($id)
    {
        try {
            $archive = Archive::where('archivable_type', 'community-segment')
                ->where('id', $id)
                ->firstOrFail();
            return response()->json($archive);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' =>   'Can only show archived community segments article'
            ], 403);
        }
    }
}