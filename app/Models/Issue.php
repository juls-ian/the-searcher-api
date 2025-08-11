<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
