# Scrapped codes in the User Model 


## activeEditorialBoard() - to get the active ed board
    public function activeEditorialBoard()
    {
        return $this->hasOne(EditorialBoard::class)->where('is_active', true);
    }

## currentEditorialBoard - to get the latest ed bord
    public function currentEditorialBoard()
    {
        return $this->hasOne(EditorialBoard::class)->latest();
    }

## currentBoardPositions
### 1.0: targeting specific columns 
```php
public function currentBoardPositionsSimplified()
{
    return $this->belongsToMany(BoardPosition::class, 'editorial_boards')
        ->wherePivot('is_current', true)
        ->withPivot('term')
        ->select('board_positions.id', 'board_positions.name', 'board_positions.category');
}

```

##  currentEditorialBoardRecords
### 1.0: helper, a little bit redundant 
```php 
    // Helper to get the current positions - returns EditorialBoard models (full record)
    public function currentEditorialBoard()
    {
        return $this->editorialBoards()
            ->where('is_current', true)
            ->with('boardPosition'); // relation to EditorialBoard model
    }

```

## currentTerm()
### 1.0: replaced with accessor instead
```php
    public function currentTerm()
    {
        if (!$this->relationLoaded('currentEditorialBoard')) {
            $this->load('currentEditorialBoard');
        }
        return $this->currentEditorialBoard?->term;
    }
```

## redundant
### getCurrentTermAttribute()
```php
   public function getCurrentTermAttribute()
    {
        return $this->editorialBoards()->latest()->value('term');
    }
```
### getAllTerms()
```php

    public function getAllTerms()
    {
        return $this->editorialBoards->pluck('term')->toArray();
    }
```
### getAllTermsCollection
```php
    public function getAllTermsCollection()
    {
        return $this->editorialBoards->pluck('term');
    }
```
