<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoardPosition extends Model
{
    /** @use HasFactory<\Database\Factories\BoardPositionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
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
        ])) {
            return 'writers (editor)';
        }

        // Artists (Editor)
        if (Str::contains($name, [
            'head artist',
            'head graphics',
            'head photojournalist',
        ])) {
            return 'artists (editor)';
        }

        // Writers (Staff)
        if (Str::contains($name, [
            'writer',
            'reporter',
        ])) {
            return 'writers (staff)';
        }

        // Artists (Staff)
        if (Str::contains($name, [
            'artist',
            'layout',
            'photojournalist',
            'illustrator',
        ])) {
            return 'artists (staff)';
        }

        return 'uncategorized';
    }
}
