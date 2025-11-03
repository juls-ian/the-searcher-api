# Scrapped codes from UserResource 

## toArray()
### 1.0: board position using onLoaded
```php 
    public function toArray(Request $request): array
    {
        return [
            // existing code
            'board_position' => $this->whenLoaded('boardPositions', function () {
                return $this->boardPositions->map(function ($boardPosition) {
                    return [
                        'board_position_id' => $boardPosition->id,
                        'position_name' => $boardPosition,
                        'term' => $boardPosition->pivot->term,
                        'is_current' => $boardPosition->pivot->is_current
                    ];
                });
            }),
            // existing code 
        ];
    }
```
### 1.1: using whenLoaded()
```php 
'board_position' => $this->when($this->relationLoaded('boardPositions'), function () {
    return $this->boardPositions->map(function ($boardPosition) {
        return [
            'board_position_id' => $boardPosition->id,
            'position_name' => $boardPosition->name,
            'category' => $boardPosition->category,
            'term' => $boardPosition->pivot->term,
            'is_current' => $boardPosition->pivot->is_current,
        ];
    });
}),
```
