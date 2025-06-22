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

    /**
     * Runs before a new article is saved in database
     */
    public function creating(Article $article)
    {
        $slug = Str::slug($article->title); # convert into slug
        $originalSlug = $slug; # store orig slug
        $count = 1;

        // Check if slug already exists in db 
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++; # if it exist, appends the number 
        }

        $article->slug = $slug; # final value 
    }

    /**
     * Runs before a new article is updated in database
     */
    public function updating(Article $article)
    {
        if ($article->isDirty('title')) { # Check if title has been modified
            $slug = Str::slug($article->title);
            $originalSlug = $slug;
            $count = 1;

            // Check if any OTHER article has this slug
            while (
                Article::where('slug', $slug) # check if slug exists
                    ->where('id', '!=', $article->id) # ensure not to compare slug to itself
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            $article->slug = $slug; # final value 
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