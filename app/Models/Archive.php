<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Archive extends Model
{
    /** @use HasFactory<\Database\Factories\ArchiveFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'archivable_type',
        'archivable_id',
        'title',
        'slug',
        'data',
        'archived_at',
        'archiver_id'
    ];

    protected $casts = [
        'data' => 'array',
        'archived_at' => 'datetime'
    ];

    // Polymorphic relationship to other models 
    public function archivable()
    {
        return $this->morphTo();
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archiver_id');
    }
}
