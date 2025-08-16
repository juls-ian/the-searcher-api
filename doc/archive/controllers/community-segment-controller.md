# Unused codes in the StoreCommunitySegmentController


## v.1 
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