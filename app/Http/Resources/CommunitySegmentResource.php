<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunitySegmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'segment_type' => $this->segment_type,
            'writer' => $this->writer->fullname,
            'series_of' => $this->series_of,
            'published_at' => $this->published_at,
            'series_order' => $this->series_order,
            'segment_cover' => $this->segment_cover,
            'cover_artist_id' => $this->coverArtist->fullname,
            'cover_caption' => $this->cover_caption,
            // Poll segments if it exists
            'poll_segments' => $this->when($this->segmentPolls, new SegmentPollResource($this->segmentPolls)),
            // Article segments if it exists
            'article_segments' => $this->when($this->segmentArticles, new SegmentArticleResource($this->segmentArticles)),


        ];
    }
}
