# Scrapped codes in SearchService

## universalSearch()
### 1.0: initial code
    public function universalSearch(string $query, int $perPage = 5)
    {
        $results = collect();

        foreach ($this->models as $type => $model) {
            # run Scout's search 
            $hits = $model::search($query)->take($perPage)->get();

            $results = $results->merge(
                # maps into a structure like this 
                $hits->map(fn($item) => [
                    'type' => $type,
                    'data' => $item
                ])
            );
        }
        return $results->values();
    }

 ## modelSearch()
 ### 1.0: initial code
     public function modelSearch(string $modelKey, string $query, int $perPage = 10)
    {
        if (! isset($this->models[$modelKey])) {
            throw new InvalidArgumentException("Model '{$modelKey}' is not searchable.");
        }

        $model = $this->models[$modelKey];

        return $model::search($query)->take($perPage)->get();
    }   

## models
### 1.0: initial code
    protected array $models = [
        'article' => Article::class,
        'archive' => Archive::class,
        'bulletin' => Bulletin::class,
        'calendar' => Calendar::class,
        'community-segment' => CommunitySegment::class,
        'editorial-board' => EditorialBoard::class,
        'issue' => Issue::class,
        'multimedia' => Multimedia::class,
        'user' => User::class
    ];