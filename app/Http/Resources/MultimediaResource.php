<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultimediaResource extends JsonResource
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
            'caption' => $this->caption,
            'published_at' => $this->published_at,
            'files' => $this->files,
            'credit_type' => $this->files_credit_type ?? null,
            'multimedia_artists' => $this->multimediaArtists->pluck('full_name'), # array of names
            'thumbnail' => $this->thumbnail,
            'thumbnail_artist' => $this->thumbnailArtist->full_name ?? null,
            'published_by' => $this->publisher->full_name ?? null
        ];
    }
}