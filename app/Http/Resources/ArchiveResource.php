<?php

namespace App\Http\Resources;

use App\Models\ArticleCategory;
use App\Models\SegmentsArticle;
use App\Models\User;
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
            'archivable_type' => $this->archivable_type,
            'archivable_id' => $this->archivable_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'data' => $this->formattedDataByType(),
            'archived_at' => $this->archived_at,
            'archiver_id' => $this->archiver->full_name ?? null,
        ];
    }
}
