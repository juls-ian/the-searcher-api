<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Multimedia extends Model
{
    /** @use HasFactory<\Database\Factories\MultimediaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'caption',
        'published_at',
        'files',
        'thumbnail',
        'thumbnail_artist_id',
        'thumbnail_credit_type'
    ];

    protected $casts = [
        'files' => 'array',
        'published_at' => 'datetime'
    ];

    protected $attributes = [
        'thumbnail_credit_type' => 'photo'
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

    public static function booted()
    {
        static::creating(function ($multimedia) {
            if (!$multimedia->publisher_id && Auth::id()) {
                $multimedia->publisher_id = Auth::id();
            }
        });
    }
}
