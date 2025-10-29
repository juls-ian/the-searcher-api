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

## seeding positions 
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
    }
