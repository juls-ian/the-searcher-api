<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SegmentsArticle extends Model
{
    /** @use HasFactory<\Database\Factories\SegmentsArticleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'segment_id',
        'body'
    ];


    public function segment()
    {
        return $this->belongsTo(CommunitySegment::class, 'segment_id');
    }
}