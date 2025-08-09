<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Multimedia;
use Exception;
use Illuminate\Support\Str;

use function PHPUnit\Framework\throwException;

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
        $baseSlug = Str::slug($article->title);
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        // Keep generating slug if it's not unique 
        while (Article::where('slug', $slug)->exists()) {
            $randomId  = Str::lower(Str::random(8));
            $slug = "{$slug}-$randomId";
        }
        $article->slug = $slug; # base final value 
    }

    /**
     * Runs before a new article is updated in database
     */
    public function updating(Article $article)
    {
        if ($article->isDirty('title')) {

            $baseSlug = Str::slug($article->title);
            $slugDate = now()->format('Y-m-d');
            $slug = "{$baseSlug}-{$slugDate}";

            // Check if the slug exists
            while (
                Article::where('slug', $slug)
                ->where('id', '!=', $article->id) # avoid comparing slug to itself 
                ->exists()
            ) {
                $randomId  = Str::lower(Str::random(8));
                $slug = "{$slug}-{$randomId}";
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
