<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorialBoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'term' => $this->term,
            'current' => $this->is_automatically_current || $this->is_current,
            'archived' => $this->is_archived,
            'member' => [
                'id' => $this->user->id,
                'full_name' => $this->user->full_name,
                'pen_name' => $this->user->pen_name,
                'board_position' => $this->user->board_position,
                'profile_pic' => $this->user->profile_pic,
                'status' => $this->user->status,
                'role' => $this->user->role,
            ],
        ];
    }
}
