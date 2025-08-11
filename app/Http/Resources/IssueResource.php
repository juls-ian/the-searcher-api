<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
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
            'description' => $this->description,
            'published_at' => $this->published_at,
            'editors' => $this->editors,
            'writers' => $this->writers,
            'photojournalists' => $this->photojournalists,
            'artists' => $this->artists,
            'layout_artists' => $this->layout_artists,
            'contributors' => $this->contributors,
            'file' => $this->issue_file,
            'thumbnail' => $this->thumbnail,
        ];
    }
}
