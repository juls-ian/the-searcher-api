<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, SoftDeletes, Archivable;

    protected $fillable = [
        'title',
        'article_category_id',
        'writer_id',
        'body',
        'published_at',
        'is_live',
        'is_header',
        'is_archived',
        'cover_photo',
        'cover_caption',
        'cover_artist_id',
        'thumbnail_same_as_cover',
        'thumbnail',
        'thumbnail_artist_id',
        'archived_at',
        'add_to_ticker',
        'ticker_expires_at',
        'publisher_id'
    ];

    // Convert incoming values automatically
    protected $casts = [
        'published_at' => 'datetime',
        'is_live' => 'boolean',
        'is_header' => 'boolean',
        'is_archived' => 'boolean',
        'thumbnail_same_as_cover' => 'boolean',
        'add_to_ticker' => 'boolean'
    ];

    // Default values 
    protected $attributes = [
        'is_live' => false,
        'is_header' => false,
        'thumbnail_same_as_cover' => false,
        'add_to_ticker' => false
    ];

    protected $dates = ['archived_at'];

    // protected 

    // relation to ArticleCategory
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    // relationship to User (writer)
    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    // relationship to User (cover_artist)
    public function coverArtist()
    {
        return $this->belongsTo(User::class, 'cover_artist_id');
    }

    public function thumbnailArtist()
    {
        return $this->belongsTo(User::class, 'thumbnail_artist_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    // self-reference relationship for series (LIVE ARTICLES)
    public function series()
    {
        return $this->belongsTo(Article::class, 'series_id');
    }

    public function seriesArticles()
    {
        return $this->hasMany(Article::class, 'series_id');
    }

    public static function booted()
    {
        /**
         * To save the publisher_id 
         *  Alternatively we can handle this in the controller:
         * $validatedArticle['publisher_id'] = auth()->id();
         */
        static::creating(function ($article) {
            // Only set publisher_id if not already set and user is authenticated
            if (!$article->publisher_id && Auth::id()) {
                $article->publisher_id = Auth::id();
            }
        });
    }
}
