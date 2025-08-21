<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'writer' => $this->writer->full_name,
            'category' => $this->category->name ?? null,
            'body' => $this->body,
            'published_at' => $this->published_at,
            'is_live' => $this->is_live,
            'is_header' => $this->is_header,
            'cover_photo' => $this->cover_photo,
            'cover_caption' => $this->cover_caption,
            'cover_artist' => $this->coverArtist->full_name ?? null,
            'thumbnail_same_as_cover' => $this->thumbnail_same_as_cover,
            'thumbnail' => $this->thumbnail,
            'thumbnail_artist' => $this->thumbnailArtist->full_name ?? null,
            'published_by' => $this->publisher->full_name ?? null
        ];
    }
}
