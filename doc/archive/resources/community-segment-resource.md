# Unused codes in the CommunitySegmentResource

## v.1
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
            'segment_type' => $this->segment_type,
            'writer_name' => $this->writer_fullname,
            'series_of' => $this->series_of,
            'published_at' => $this->published_at,
            'series_order' => $this->series_order,
            'segment_cover' => $this->segment_cover,
            'cover_artist' => $this->coverArtist_fullname,
            'cover_caption' => $this->cover_caption,


            // Use specific resources for each type
            'poll' => new SegmentPollResource($this->whenLoaded('segmentPoll')),
            'article' => new SegmentArticleResource($this->whenLoaded('segmentArticle')),
        ];

    }
}

## v.1.1
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunitySegmentResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'segment_type' => $this->segment_type,
            'writer_name' => $this->writer_fullname,
            'series_of' => $this->series_of,
            'published_at' => $this->published_at,
            'series_order' => $this->series_order,
            'segment_cover' => $this->segment_cover,
            'cover_artist' => $this->coverArtist_fullname,
            'cover_caption' => $this->cover_caption,

            // Include data based on segment type
            'poll_data' => $this->when($this->segment_type === 'poll', function () {
                return $this->whenLoaded('segmentPoll');
            }),

            'article_data' => $this->when($this->segment_type === 'article', function () {
                return $this->whenLoaded('segmentArticle');
            }),

            // other option
            
            'poll' => $this->whenLoaded('pollSegments'),
            'article' => $this->whenLoaded('articleSegments')

        ];

    }
}

## v.1.2


## v.1.3
    private function getSegmentContent()
    {
        switch ($this->segment_type) {
            case 'poll':
                return $this->whenLoaded('segmentPoll');
            case 'article':
                return $this->whenLoaded('segmentArticle');
            default:
                return null;
        }
    }