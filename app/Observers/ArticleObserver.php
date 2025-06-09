<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        //
    }

    public function creating(Article $article)
    {
        $slug = Str::slug($article->title);
        $originalSlug = $slug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $article->slug = $slug;
    }

    public function updating(Article $article)
    {
        if ($article->isDirty('title')) {
            $slug = Str::slug($article->title);
            $originalSlug = $slug;
            $count = 1;
            while (
                Article::where('slug', $slug)
                    ->where('id', '!=', $article->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }
            $article->slug = $slug;
        }
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}