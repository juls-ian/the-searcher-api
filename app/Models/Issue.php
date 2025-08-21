<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Issue extends Model
{
    /** @use HasFactory<\Database\Factories\IssueFactory> */
    use HasFactory;

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
        'issue_file',
        'thumbnail',
        'publisher_id'
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
