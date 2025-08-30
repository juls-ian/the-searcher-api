<?php

namespace App\Providers;

use App\Models\Archive;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Bulletin;
use App\Models\Calendar;
use App\Models\CommunitySegment;
use App\Models\Issue;
use App\Models\Multimedia;
use App\Observers\ArchiveObserver;
use App\Observers\ArticleCategoryObserver;
use App\Observers\UserObserver;
use App\Observers\ArticleObserver;
use App\Observers\BulletinObserver;
use App\Observers\CalendarObserver;
use App\Observers\CommunitySegmentObserver;
use App\Observers\IssueObserver;
use App\Observers\MultimediaObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
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
        Archive::observe(ArchiveObserver::class);
        Calendar::observe(CalendarObserver::class);


        // Shorten the name of the archivable_type 
        Relation::enforceMorphMap([
            'article' => 'App\Models\Article',
            'user' => 'App\Models\User',
            'community-segment' => 'App\Models\CommunitySegment',
            'bulletin' => 'App\Models\Bulletin',
            'editorial-board' => 'App\Models\EditorialBoard',
            'issue' => 'App\Models\Issue',
            'multimedia' => 'App\Models\Multimedia'
        ]);

        // By default route model binding does not work on soft deleted models, hence me must customize binding to include trashed models 
        Route::bind('article', function (string $value) {
            return Article::withTrashed()->where('id', $value)->firstOrFail(); # for forceDestroy & restore 
        });
        Route::bind('multimedia', function (string $value) {
            return Multimedia::withTrashed()->where('id', $value)->firstOrFail();
        });
        Route::bind('community-segment', function (string $value) {
            return CommunitySegment::withTrashed()->where('id', $value)->firstOrFail();
        });

        Route::bind('bulletin', function (string $value) {
            return Bulletin::withTrashed()->where('id', $value)->firstOrFail();
        });

        Route::bind('issue', function (string $value) {
            return Issue::withTrashed()->where('id', $value)->firstOrFail();
        });
    }
}
