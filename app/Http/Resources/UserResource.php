<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'full_name' => $this->full_name,
            'fn_slug' => $this->full_name_slug,
            'pen_name' => $this->pen_name,
            'staff_id' => $this->staff_id,
            'email' => $this->email,
            'board_position' => $this->boardPositions->map(function ($boardPosition) { // relationship to Board
                return [
                    'board_position_id' => $boardPosition->id,
                    'position_name' => $boardPosition,
                    'term' => $boardPosition->pivot->term,
                    'is_current' => $boardPosition->pivot->is_current
                ];
            }),
            'year' => $this->year_level,
            'course' => $this->course,
            'phone' => $this->phone,
            'role' => $this->role,
            'current_term' => $this->currentTerm(),
            // editorialBoards relation must be loaded first in the controller
            'all_terms' => $this->whenLoaded('editorialBoards', function () {
                return $this->editorialBoards->pluck('term'); // 'term' from EdBoard table
            }, []),
            'status' => $this->status,
            'joined_at' => $this->joined_at,
            'left_at' => $this->left_at,
            'profile_pic' => $this->profile_pic,
        ];
    }
}
