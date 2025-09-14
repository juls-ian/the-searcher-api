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

    /**
     * Archive Filter  
     */
    public function searchArchives(array $params)
    {
        $query = $params['q'] ?? '*';
        $year  = $params['year'] ?? null;
        $month = $params['month'] ?? null;
        $sort  = $params['sort'] ?? 'desc';
        $from  = $params['from'] ?? null;
        $to    = $params['to'] ?? null;

        $search = Archive::search($query, function ($meilisearch, $query, $options) use ($year, $month, $from, $to) {

            $filters = [];



            // Case 1: Year + Month - specific month in a year
            if ($year && $month) {

                // Convert month name to number if necessary
                if (!is_numeric($month)) {
                    $month = date('m', strtotime($month));
                } else {
                    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                }

                $start = sprintf('%04d-%02d-01', $year, $month); # $year = 2025, $month = 9 → "2025-09-01"
                $end = date('Y-m-d', strtotime("$start +1 month")); # $start = "2025-09-01" → $end = "2025-10-01"
                # builds the filter 
                $filters[] = "archived_at >= $start AND archived_at < $end"; # archived_at >= 2025-09-01 AND archived_at < 2025-10-01
            }
            // Case 4: Custom date range  
            elseif ($from && $to) {
                # interpret $from & $to as years if both are 4 digits 
                if (strlen($from) === 4 && strlen($to) === 4) {
                    $start = "$from-01-01";
                    # converts $to (string) to int when +1 = 2025 + 1 = 2026
                    $end = ($to + 1) . "-01-01"; # exclusive upper bound trick (< 2026-01-01)
                    $filters[] = "archived_at >= $start AND archived_at < $end";
                } else {
                    # fallback
                    $filters[] = "archived_at >= $from AND archived_at <= $to";
                }
            }
            // Case 2: Year only 
            elseif ($year) {
                $filters[] = "year = $year";
            }
            // Case 3: Month only
            elseif ($month) {
                $filters[] = "month = \"$month\""; # needs quotes since it's a string in Meilisearch
            }

            /**
             * takes ["year = 2024", "month = \"05\""] and implode turn it into a single string 
             * "year = 2024 AND month = \"05\""
             */
            if ($filters) {
                $options['filter'] = implode(' AND ', $filters);
            }

            return $meilisearch->search($query, $options);
        });

        return $search->orderBy('archived_at', $sort)->paginate(10);
    }
}
