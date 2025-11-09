# Scrapped codes in DatabaseSeeder

## Initial code
```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ArticleCategorySeeder::class,
            IssueSeeder::class,
            CalendarSeeder::class,
            ArchiveSeeder::class,
            EditorialBoardSeeder::class,
            ArticleSeeder::class,
            BulletinSeeder::class,
            MultimediaSeeder::class,
            CommunitySegmentSeeder::class,
        ]);
    }
}
```

## seeding board position - foreach
### 1.0: using board position seeder instead of factory
```php
public function run(): void
{
    $this->call(BoardPositionSeeder::class); // must run first before seeder

    foreach ($users as $user) 
        // Get all positions
        $positions = BoardPosition::all()
        // Pick based on role
        $boardPosition = match ($user->role) {
            'admin' => $positions->where('category', 'executive')->random(),
            'editor' => $positions->whereIn('category', ['writers (editor)', 'artists (editor)'])->random(),
            default => $positions->whereIn('category', ['writers (staff)', 'artists (staff)'])->random(),
        }
        // Assigning board position
        $user->update([
            'board_position_id' => $boardPosition->id,
        ]);
        // existing codes
    }
```
### 1.1: using the factory instance
```php
public function run(): void
{
    BoardPosition::factory()->createDefaultPositions();

    foreach ($users as $user) 
        // Get all positions
        $positions = BoardPosition::all()
        // Pick based on role
        $boardPosition = match ($user->role) {
            'admin' => $positions->where('category', 'executive')->random(),
            'editor' => $positions->whereIn('category', ['writers (editor)', 'artists (editor)'])->random(),
            default => $positions->whereIn('category', ['writers (staff)', 'artists (staff)'])->random(),
        }
        // Assigning board position
        $user->update([
            'board_position_id' => $boardPosition->id,
        ]);

        // existing codes
    }
```
### 1.2: attaching board position via pivot table
```php
        foreach ($users as $user) {

            // Get all positions
            $positions = BoardPosition::all();

            // Pick based on role
            $boardPosition = match ($user->role) {
                'admin' => $positions->where('category', 'executive')->random(),
                'editor' => $positions->whereIn('category', ['writers (editor)', 'artists (editor)'])->random(),
                default => $positions->whereIn('category', ['writers (staff)', 'artists (staff)'])->random(),
            };

            // Attaching board position
            $user->boardPositions()->attach($boardPosition->id);

            // At least one current active term for each user
            EditorialBoard::factory()->create([
                'user_id' => $user->id,
                'term' => '2025-2026',
                'is_current' => true
            ]);

            // Optional: additional 1-2 previous terms
            $additionalTerms = rand(0, 2);
            for ($i = 0; $i < $additionalTerms; $i++) {
                $startYear = 2020 + $i;
                $endYear = $startYear + 1;

                EditorialBoard::factory()->create([
                    'user_id' => $user->id,
                    'term' => "{$startYear}-{$endYear}",
                    'is_current' => false
                ]);
            }
        }
```
### 1.3: new version - longer code
```php 
foreach ($users as $user) {

    // Get all positions
    $positions = BoardPosition::all();

    // Pick based on role
    $primaryPosition = match ($user->role) {
        'admin' => $positions->where('category', 'executive')->random(),
        'editor' => $positions->whereIn('category', ['writers (editor)', 'artists (editor)'])->random(),
        default => $positions->whereIn('category', ['writers (staff)', 'artists (staff)'])->random(),
    };

    // Optional 2nd position
    $secondaryPosition = null;
    if (rand(1, 10) <= 2) {
        $secondaryPosition = $positions->where('id', '!=', $primaryPosition->id)->random();
    }


    // At least one current active term for each user, user_id is automatically added with create()
    $user->editorialBoards()->create([
        'term' => '2025-2026',
        'board_position_id' => $primaryPosition->id,
        'is_current' => true
    ]);

    if ($secondaryPosition) {
        $user->editorialBoards()->create([
            'term' => '2025-2026',
            'board_position_id' => $secondaryPosition->id,
            'is_current' => true
        ]);
    }

    // Optional: additional 1-2 previous terms
    $additionalTerms = rand(0, 2);
    for ($i = 0; $i < $additionalTerms; $i++) {
        $startYear = 2020 + $i;
        $endYear = $startYear + 1;

        // Pick a random position for historical term
        $historicalPosition = $positions->random();

        EditorialBoard::factory()->create([
            'term' => "{$startYear}-{$endYear}",
            'board_position_id' => $historicalPosition->id,
            'is_current' => false
        ]);
    }
}
```
### 1.4: historical EditorialBoard is created using Factory: creates 37 users 
```php
foreach ($users as $user) {
    // Get all positions
    $positions = BoardPosition::all();
    $positionCount = (rand(1, 10) <= 2) ? 2 : 1;  // 20% get 2 positions
    // Pick based on role
    $selectedPositions = match ($user->role) {
        'admin' => $positions->where('category', 'executive')->random($positionCount),
        'editor' => $positions->whereIn('category', ['writers (editor)', 'artist (editor)'])->random($positionCount),
        default => $positions->whereIn('category', ['writers (staff)', 'artist (staff)'])->random($positionCount),
    };
    // Create current term entry for each position
    foreach ($selectedPositions as $position) {
        $user->editorialBoards()->create([
            'term' => '2025-2026',
            'board_position_id' => $position->id,
            'is_current' => true
        ]);
    }
    // Optional: additional 1-2 previous terms
    $additionalTerms = rand(0, 2);
    for ($i = 0; $i < $additionalTerms; $i++) {
        $startYear = 2020 + $i;
        $endYear = $startYear + 1;
        // Pick a random position for historical term
        $historicalPosition = $positions->random();
        EditorialBoard::factory()->create([
            'term' => "{$startYear}-{$endYear}",
            'board_position_id' => $historicalPosition->id,
            'is_current' => false
        ]);
    }
}
```

## seeding communitySegment
### 1.0: expecting an array 
```php 
public function run() 
{
    // Seeding community segments
    this->seedCommunitySegments($users);
}

