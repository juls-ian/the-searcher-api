<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}