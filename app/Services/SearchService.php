<?php

namespace App\Services;

use App\Http\Resources\ArchiveResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\BulletinResource;
use App\Http\Resources\CalendarResource;
use App\Http\Resources\CommunitySegmentResource;
use App\Http\Resources\EditorialBoardResource;
use App\Http\Resources\IssueResource;
use App\Http\Resources\MultimediaResource;
use App\Http\Resources\UserResource;
use App\Models\Archive;
use App\Models\Article;
use App\Models\Bulletin;
use App\Models\Calendar;
use App\Models\CommunitySegment;
use App\Models\EditorialBoard;
use App\Models\Issue;
use App\Models\Multimedia;
use App\Models\User;
use InvalidArgumentException;

class SearchService
{
    // Map short keys 
    protected array $models = [
        'article' => [Article::class, ArticleResource::class],
        'archive' => [Archive::class, ArchiveResource::class],
        'bulletin' => [Bulletin::class, BulletinResource::class],
        'calendar' => [Calendar::class, CalendarResource::class],
        'community-segment' => [CommunitySegment::class, CommunitySegmentResource::class],
        'editorial-board' => [EditorialBoard::class, EditorialBoardResource::class],
        'issue' => [Issue::class, IssueResource::class],
        'multimedia' => [Multimedia::class, MultimediaResource::class],
        'user' => [User::class, UserResource::class]
    ];

    /**
     * Universal searching
     */
    public function universalSearch(string $query, int $perPage = 5)
    {
        $results = collect();

        foreach ($this->models as $type => [$model, $resource]) {
            # run Scout's search | $hits = list of matching documents
            $hits = $model::search($query)->take($perPage)->get();

            $results = $results->merge(
                # maps into a structure like this 
                $hits->map(fn($item) => [
                    'type' => $type,
                    'data' => new $resource($item)
                ])
            );
        }
        return $results->values();
    }

    /**
     * Single model search 
     */
    public function modelSearch(string $modelKey, string $query, int $perPage = 10)
    {
        if (! isset($this->models[$modelKey])) {
            throw new InvalidArgumentException("Model '{$modelKey}' is not searchable.");
        }

        [$model, $resource] = $this->models[$modelKey];
        $hits = $model::search($query)->take($perPage)->get();

        return $resource::collection($hits);
    }
}
