<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Multimedia extends Model
{
    /** @use HasFactory<\Database\Factories\MultimediaFactory> */
    use HasFactory, SoftDeletes, Archivable;

    protected $fillable = [
        'title',
        'category',
        'caption',
        'published_at',
        'files',
        'files_credit_type',
        'thumbnail',
        'thumbnail_artist_id',
        'archived_at'
    ];

    protected $casts = [
        'files' => 'array',
        'published_at' => 'datetime'
    ];

    protected $attributes = [
        'files_credit_type' => 'photo'
    ];


    public function multimediaArtists()
    {
        /**
         * # if pivot table follows Laravel naming convention, we can simplify it to User:class: 
         * assuming the pivot table is "multimedia_user" and we have column of "multimedia_id" and "user_id"
         */
        return $this->belongsToMany(User::class, 'multimedia_user', 'multimedia_id', 'user_id');
    }

    public function thumbnailArtist()
    {
        return $this->belongsTo(User::class, 'thumbnail_artist_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    // public function scopeArchivedMultimedia($query)
    // {
    //     return $query->where('archivable_type', 'multimedia');
    // }

    public static function booted()
    {
        static::creating(function ($multimedia) {
            if (!$multimedia->publisher_id && Auth::id()) {
                $multimedia->publisher_id = Auth::id();
            }
        });
    }
}