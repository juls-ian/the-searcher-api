<?php

namespace App\Observers;

use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ArticleCategoryObserver
{
    /**
     * Handle the ArticleCategory "created" event.
     */
    public function created(ArticleCategory $articleCategory): void
    {
        //
    }

    public function creating(ArticleCategory $category)
    {
        $slug = Str::slug($category->name);
        $originalSlug = $slug;
        $count = 1;

        /**
         * This block of code is optional since I added unique name validator in 
         * the request of the controller 
         * still a good thing to have just in case 
         */
        $query = ArticleCategory::where('slug', $slug); # base query 

        // SCOPE 1: if category has a parent, only check for duplicates under same parent 
        if ($category->parent_id !== null) {
            // Check if slug exists among siblings in the same parent category 
            $query->where('parent_id', $category->parent_id);
        } else {
            // SCOPE 2: if parent_id === null, check among other top-level category
            $query->whereNull('parent_id'); # uniqueness in parents
        }

        // Keep looping until a unique slug is found
        while (
            ArticleCategory::where('slug', $slug)
            // Scope 1: if parent_id exists, check siblings
            ->when($category->parent_id !== null, fn($q) =>
            $q->where('parent_id', $category->parent_id))
            // Scope 2: if parent_id = null, check other parent 
            ->when($category->parent_id === null, fn($q) =>
            $q->whereNull('parent_id'))
            // Scope 3: if updating existing category, exclude itself from check 
            ->when(isset($category->id), fn($q) =>
            $q->where('id', '!=', $category->id))
            // Check if a record with this slug exists within the determined scope 
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }


        $category->slug = $slug; # final slug value 


    }

    public function updating(ArticleCategory $category)
    {
        // Proceed only if 'name' attribute changed 
        if ($category->isDirty('name')) {
            $slug = Str::slug($category->name);
            $originalSlug = $slug;
            $count = 1;

            /**
             * This block of code is optional since I added unique name validator in 
             * the request of the controller 
             * still a good thing to have just in case 
             */
            // Base query 
            $query = ArticleCategory::where('slug', $slug)
                ->where('id', '!=', $category->id); # exclude self 

            // Scope by parent_id
            if ($category->parent_id !== null) {
                $query->where('parent_id', $category->parent_id);
            } else {
                $query->whereNull('parent_id');
            }

            // Keep looping until a unique slug is found
            while (
                ArticleCategory::where('slug', $slug)
                // Scope 1: if parent_id exists, check siblings
                ->when($category->parent_id !== null, fn($q) =>
                $q->where('parent_id', $category->parent_id))
                // Scope 2: if parent_id = null, check other parent 
                ->when($category->parent_id === null, fn($q) =>
                $q->whereNull('parent_id'))
                // Scope 3: if updating existing category, exclude itself from check 
                ->when(isset($category->id), fn($q) =>
                $q->where('id', '!=', $category->id))
                // Check if a record with this slug exists within the determined scope 
                ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            $category->slug = $slug; # final value
        }
    }

    /**
     * Handle the ArticleCategory "updated" event.
     */
    public function updated(ArticleCategory $articleCategory): void
    {
        //
    }

    /**
     * Handle the ArticleCategory "deleted" event.
     */
    public function deleted(ArticleCategory $articleCategory): void
    {
        //
    }

    /**
     * Handle the ArticleCategory "restored" event.
     */
    public function restored(ArticleCategory $articleCategory): void
    {
        //
    }

    /**
     * Handle the ArticleCategory "force deleted" event.
     */
    public function forceDeleted(ArticleCategory $articleCategory): void
    {
        //
    }
}
