<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Issue extends Model
{
    /** @use HasFactory<\Database\Factories\IssueFactory> */
    use HasFactory, SoftDeletes, Archivable, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'published_at',
        'editors',
        'writers',
        'photojournalists',
        'artists',
        'layout_artists',
        'contributors',
        'file',
        'thumbnail',
        'publisher_id',
        'archived_at'
    ];

    protected $casts = [
        'editors' => 'array',
        'writers' => 'array',
        'photojournalists' => 'array',
        'artists' => 'array',
        'layout_artists' => 'array',
        'contributors' => 'array',
        'published_at' => 'datetime',
    ];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public static function booted()
    {
        static::creating(function ($issue) {
            if (!$issue->publisher_id && Auth::id()) {
                $issue->publisher_id = Auth::id();
            }
        });
    }
}
