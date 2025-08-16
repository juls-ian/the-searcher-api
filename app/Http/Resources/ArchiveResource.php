<?php

namespace App\Http\Resources;



use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveResource extends JsonResource
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
            'archive_type' => $this->archivable_type,
            'archived_id' => $this->archivable_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'data' => $this->data,
            'archived_at' => $this->archived_at,
            'archiver_id' => $this->archiver_id,
        ];
    }
}
