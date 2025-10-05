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