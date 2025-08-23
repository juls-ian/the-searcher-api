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