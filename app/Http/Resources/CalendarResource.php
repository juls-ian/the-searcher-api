<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarResource extends JsonResource
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
            'event_type' => $this->event_type,
            'start_at' => $this->start_at,
            'ends_at' => $this->ends_at,
            'is_allday' => $this->is_allday,
            'venue' => $this->venue,
            'details' => $this->details,
            'is_public' => $this->is_public,
            'status' => $this->status
        ];
    }
}
