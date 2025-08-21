<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Bulletin extends Model
{
    /** @use HasFactory<\Database\Factories\BulletinFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'writer_id',
        'details',
        'published_at',
        'cover_photo',
        'cover_artist_id',
        'publisher_id'
    ];

    public function casts()
    {
        return [
            'published_at' => 'datetime',
            'cover_photo' => 'string'
        ];
    }

    // Relationship to User 
    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function coverArtist()
    {
        return $this->belongsTo(User::class, 'cover_artist_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public static function booted()
    {
        static::creating(function ($bulletin) {
            if (!$bulletin->publisher_id && Auth::id()) {
                $bulletin->publisher_id = Auth::id();
            }
        });
    }
}
