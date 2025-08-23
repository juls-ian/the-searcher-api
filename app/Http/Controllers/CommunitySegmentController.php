<?php

namespace App\Http\Controllers;

use App\Models\CommunitySegment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommunitySegmentRequest;
use App\Http\Requests\UpdateCommunitySegmentRequest;
use App\Http\Resources\CommunitySegmentResource;
use App\Models\SegmentsArticle;
use App\Models\SegmentsPoll;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunitySegmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', CommunitySegment::class);
        $segments = CommunitySegment::with(['writer', 'coverArtist', 'series', 'segmentArticles', 'segmentPolls'])->get();
        return CommunitySegmentResource::collection($segments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunitySegmentRequest $request)
    {
        $this->authorize('create', CommunitySegment::class);
        $validatedSegment = $request->validated();

        // Handler 1: segment_cover upload 
        if ($request->hasFile('segment_cover')) {
            $coverPath = $request->file('segment_cover')->store('community-segments/covers', 'public');
            $validatedSegment['segment_cover'] = $coverPath;
        }

        // Handler 2: published_at date/time 
        if (isset($validatedSegment['published_at']) && $validatedSegment['published_at']) {
            # if set, convert it to Carbon instance 
            $validatedSegment['published_at'] = Carbon::parse($validatedSegment['published_at']);
        } else {
            # else set to current time 
            $validatedSegment['published_at'] = Carbon::now();
        }

        // Handler 3: set series_order automatically to 1
        if (isset($validatedSegment['series_type']) && $validatedSegment['series_type'] === 'series_header') {
            $validatedSegment['series_order'] = 1;
        }

        $segment = CommunitySegment::create($validatedSegment);


        if ($validatedSegment['segment_type'] === 'article') {
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


        // Eager load relationships to User 
        $segment->load(['writer', 'coverArtist', 'series', 'segmentArticles', 'segmentPolls']);
        return CommunitySegmentResource::make($segment);
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunitySegment $communitySegment)
    {
        $this->authorize('view', $communitySegment);
        $communitySegment->load(['writer', 'coverArtist', 'series', 'segmentPolls', 'segmentArticles'])->get();
        return CommunitySegmentResource::make($communitySegment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunitySegment $communitySegment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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

            // Only update if article data is provided 
            if ($articleData && isset($articleData['body'])) {
                SegmentsArticle::where('segment_id', $communitySegment->id)->update([
                    'body' => $articleData['body']
                ]);
            }
        } else if ($communitySegment->segment_type === 'poll') {
            // Accessing poll data from poll_segments key 
            $pollData = $request->input('poll_segments'); # input() for nested data 

            SegmentsPoll::where('segment_id', $communitySegment->id)->update([
                'question' => $pollData['question'],
                'options' => $pollData['options'],
                'ends_at' => $pollData['ends_at']
            ]);
        }


        # reload relationships
        $communitySegment->load(['writer', 'coverArtist', 'series', 'segmentPolls', 'segmentArticles']);
        return CommunitySegmentResource::make($communitySegment);
    }

    /**
     * Remove the specified resource from storage.
     */
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
}
