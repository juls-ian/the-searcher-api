<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class CommunitySegment extends Model
{
    /** @use HasFactory<\Database\Factories\CommunitySegmentFactory> */
    use HasFactory, SoftDeletes, Archivable, Searchable;

    protected $fillable = [
        'title',
        'segment_type',
        'series_type',
        'writer_id',
        'published_at',
        'series_of',
        'series_order',
        'segment_cover',
        'credit_type',
        'cover_artist_id',
        'cover_caption',
        'publisher_id',
        'archived_at'

    ];


    protected $casts = [
        'published_at' => 'datetime'
    ];

    // Defaults 
    protected $attributes = [
        'credit_type' => 'photo'
    ];

    // For the search engine
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'writer' => $this->writer?->full_name,
            'cover_artist' => $this->coverArtist?->full_name,
            'body' => $this->segment_type === 'article'
                ? $this->segmentArticles?->body
                : null,
            'question' => $this->segment_type === 'poll'
                ? $this->segmentPolls?->question
                : null,
            'segment_type' => $this->segment_type

        ];
    }

    /**
     * Relationships
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, CommunitySegment>
     */
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
        return $this->belongsTo(User::class, 'cover_artist_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function segmentArticles()
    {
        return $this->hasOne(SegmentsArticle::class, 'segment_id');
    }

    public function segmentPolls()
    {
        return $this->hasOne(SegmentsPoll::class, 'segment_id');
    }

    public static function booted()
    {
        static::creating(function ($segment) {
            if (!$segment->publisher_id && Auth::id()) {
                $segment->publisher_id = Auth::id();
            }
        });

        static::deleting(function ($segment) {
            // Soft delete children table if parent is deleted
            if ($segment->segment_type === 'article') {
                $segment->segmentArticles()->delete();
            } else if ($segment->segment_type === 'poll') {
                $segment->segmentPolls()->delete();
            }
        });

        static::restoring(function ($segment) {
            // Restore children if parent is restored 
            if ($segment->segment_type === 'segmentArticles') {
                $segment->article()->withTrashed()->first()?->restore();
            } else if ($segment->segment_type === 'segmentPolls') {
                $segment->segmentPolls()->withTrashed()->first()?->restore();
            }
        });
    }

    # series_of mutator
    public function setSeriesOfAttribute($value)
    {
        $this->attributes['series_of'] = ($this->segment_type === 'article') ? $value : null;
    }
}
