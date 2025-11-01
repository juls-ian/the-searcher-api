<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class EditorialBoard extends Model
{
    use HasFactory, Searchable;
    protected $fillable = [
        'term',
        'board_position_id',
        'is_current'
    ];

    // For the search engine
    public function toSearchableArray()
    {
        return [
            'term' => $this->term
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boardPosition()
    {
        return $this->belongsTo(BoardPosition::class);
    }

    // Computed attribute 1: is this term current based on year?
    public function getIsAutomaticallyCurrentAttribute()
    {
        // Term format "2025-2026"
        [$startYear, $endYear] = explode('-', $this->term);

        $currentYear = now()->year;

        /**
         * Checks if the current year falls between $startYear and $endYear (inclusive)
         * If $term = "2025-2026" and $currentYear = 2025 → returns true.
         *If $term = "2025-2026" and $currentYear = 2026 → returns true.
         *If $term = "2025-2026" and $currentYear = 2024 → returns false.
         */
        return $currentYear >= (int)  $startYear && $currentYear <= (int) $endYear;
    }

    // Computed attribute 2: is this term archived?
    public function getIsArchivedAttribute()
    {
        return !$this->is_automatically_current;
    }
}
