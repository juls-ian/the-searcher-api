<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BulletinResource extends JsonResource
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
            'category' => $this->category,
            'writer' => $this->writer->full_name ?? null,
            'details' => $this->details,
            'published_at' => $this->published_at,
            'cover_photo' => $this->cover_photo,
            'cover_artist' => $this->coverArtist->full_name ?? null,
            'publisher_id' => $this->publisher->full_name ?? null
        ];
    }
}
