<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SegmentsPoll extends Model
{
    /** @use HasFactory<\Database\Factories\SegmentsPollFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'segment_id',
        'question',
        'options',
        'ends_at'
    ];

    protected $casts = [
        'options' => 'array',
        'ends_at' => 'datetime'
    ];

    public function segment()
    {
        return $this->belongsTo(CommunitySegment::class, 'segment_id');
    }


}