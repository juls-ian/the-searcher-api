<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardPosition extends Model
{
    /** @use HasFactory<\Database\Factories\BoardPositionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'editorial_boards') // intermediate tbl
            ->withPivot('term', 'is_current') // columns from intermediate tbl
            ->withTimestamps() // timestamp columns from intermediate tbl
        ;
    }

    public static function booted()
    {
        static::creating(function ($position) {
            if (empty($position->category)) {
                $position->category = self::determineCategory($position->name);
            };
        });
    }

    /**
     * Determine category automatically based on position name.
     * changed from private to public static to be available outside the class
     * to call BoardPosition::determineCategory($name) in the factory
     */
    public static function determineCategory(string $name): string
    {
        $name = Str::lower($name);

        // Executives
        if (Str::contains($name, [
            'editor-in-chief',
            'managing editor',
            'associate editor',
            'assoc. managing editor',
            'circulation manager',
            'circulation',
            'associate',
            'managing'
        ])) {
            return 'executive';
        }

        // Writers (Editor)
        if (Str::contains($name, [
            'copy editor',
            'news editor',
            'feature editor',
            'literary editor',
            'community editor',
            'sports editor',
            'opinion editor',
        ])) {
            return 'writer (editor)';
        }

        // Artists (Editor)
        if (Str::contains($name, [
            'head artist',
            'head graphics',
            'head photojournalist',
            'chief artist',
            'head videographer',
            'chief illustrator'
        ])) {
            return 'artist (editor)';
        }

        // Writers (Staff)
        if (Str::contains($name, [
            'writer',
            'junior writer',
            'senior writer'
        ])) {
            return 'writer (staff)';
        }

        // Artists (Staff)
        if (Str::contains($name, [
            'artist',
            'videographer',
            'graphics & layout artist',
            'photojournalist',
            'senior artist',
            'junior photojournalist',
            'senior photojournalist',
            'senior illustrator'
        ])) {
            return 'artist (staff)';
        }

        return 'uncategorized';
    }
}
