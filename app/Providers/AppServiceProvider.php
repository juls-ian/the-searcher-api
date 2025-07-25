<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\CommunitySegment;
use App\Observers\ArticleCategoryObserver;
use App\Observers\UserObserver;
use App\Observers\ArticleObserver;
use App\Observers\CommunitySegmentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Article::observe(ArticleObserver::class);
        User::observe(UserObserver::class);
        ArticleCategory::observe(ArticleCategoryObserver::class);
        CommunitySegment::observe(CommunitySegmentObserver::class);
    }
}