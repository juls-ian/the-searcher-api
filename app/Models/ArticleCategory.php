<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleCategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    // Self-referencing: parent categories 
    public function parent()
    {
        return $this->belongsTo(ArticleCategory::class, 'parent_id');
    }

    // Self-referencing: children categories 
    public function children()
    {
        return $this->hasMany(ArticleCategory::class, 'parent_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
