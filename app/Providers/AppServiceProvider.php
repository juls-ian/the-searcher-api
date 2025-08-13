<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Bulletin;
use App\Models\CommunitySegment;
use App\Models\Issue;
use App\Models\Multimedia;
use App\Observers\ArticleCategoryObserver;
use App\Observers\UserObserver;
use App\Observers\ArticleObserver;
use App\Observers\BulletinObserver;
use App\Observers\CommunitySegmentObserver;
use App\Observers\IssueObserver;
use App\Observers\MultimediaObserver;
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
        Multimedia::observe(MultimediaObserver::class);
        Issue::observe(IssueObserver::class);
        Bulletin::observe(BulletinObserver::class);
    }
}
