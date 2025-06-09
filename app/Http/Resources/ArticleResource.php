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
            'writer' => $this->writer->full_name,
            'category' => $this->category_id,
            'body' => $this->body,
            'published_at' => $this->published_at,
            'cover_photo' => $this->cover_photo,
            'cover_caption' => $this->cover_caption,
            'cover_artist' => $this->coverArtist->full_name ?? null,
            'thumbnail_same_as_cover' => $this->thumbnail_same_as_cover,
            'thumbnail' => $this->thumbnail,
            'thumbnail_caption' => $this->cover_caption,
            'thumbnail_artist' => $this->thumbnailArtist->full_name ?? null
        ];
    }
}