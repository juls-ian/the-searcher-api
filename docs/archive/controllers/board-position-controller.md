# Unused codes in the BoardPositionController

## index
### 1.0: without values() - messier 
we get a structure like this:
```json 

    "data": {
        "Editor-in-Chief": {
            "position": "Editor-in-Chief",
            "holders": [
                {
                    "id": 60,
                    "full_name": "Ian Valdez"
                }
            ]
        },
        "Managing Editor": {
            "position": "Managing Editor",
            "holders": [
                {
                    "id": 61,
                    "full_name": "Jean Grey"
                }
            ]
        },
```

```php 
    public function index()
    {
        $this->authorize('viewAny', BoardPosition::class);
        $boardPosition = BoardPosition::with('users')
            ->get()
            ->groupBy('name')
            ->map(function ($group, $position) { // group = Collection & position = key
                return [
                    'position' => $position,
                    'holders' => $group->flatMap(function ($boardPos) {
                        // 'users' (plural) and flatMap to handle the collection
                        return $boardPos->users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'full_name' => $user->full_name
                            ];
                        });
                    })

                ];
            });
          
        return response()->json(['data' => $boardPosition]);
    }
```
### 1.1: other version 
```php 
public function index()
{
    $this->authorize('viewAny', BoardPosition::class);

    $boardPositions = BoardPosition::with('users')->get()
        ->groupBy('name')
        ->map(fn ($positions, $name) => [
            'position' => $name,
            'occupants' => $positions
                ->pluck('users')     // get all users collections
                ->flatten()          // merge them into one collection
                ->unique('id')       // avoid duplicates if any
                ->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                ])
                ->values(),
        ])
        ->values();

    return response()->json(['data' => $boardPositions]);
}
```
