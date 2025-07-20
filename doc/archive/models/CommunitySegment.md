# Unused codes in the Community Segment model


## v1
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunitySegment extends Model
{
    /** @use HasFactory<\Database\Factories\CommunitySegmentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'segment_type',
        'writer_id',
        'published_at',
        'series_of',
        'series_order',
        'segment_cover',
        'cover_artist_id',
        'cover_caption'
    ];


    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function series()
    {
        return $this->hasMany(self::class, 'series_of');
    }

    public function coverArtist()
    {
        return $this->belongsTo(User::class, 'cover_artist');
    }

    // Polymorphism relationship
    public function segmentable()
    {
        return $this->morphTo();
    }



}