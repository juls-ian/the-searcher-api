<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
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
            'category' => $this->category->name ?? null,
            'writer' => in_array($this->category->name, ['Opinion', 'Column', 'Editorial'])
                ? $this->writer->pen_name
                : $this->writer->full_name,
            'body' => $this->body,
            'published_at' => $this->published_at?->toFormattedDateString(),
            'published_at_full' => $this->published_at?->format('F j, Y'),
            'is_live' => $this->is_live,
            'is_header' => $this->is_header,
            'cover_photo' => $this->cover_photo ? Storage::url($this->cover_photo) : null,
            'cover_caption' => $this->cover_caption,
            'cover_artist' => $this->coverArtist->full_name ?? null,
            'credit_type' => $this->cover_credit_type,
            'thumbnail_same_as_cover' => $this->thumbnail_same_as_cover,
            'thumbnail' => $this->thumbnail ? Storage::url($this->thumbnail) : null,
            'thumbnail_artist' => $this->thumbnailArtist->full_name ?? null,
            'published_by' => $this->publisher->full_name ?? null
        ];
    }
}
