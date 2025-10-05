# Scrapped codes in MultimediaSeeder

## initial code
```php
class MultimediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Multimedia::factory()
            ->count(10)
            ->create()
            ->each(function ($multimedia) {
                $users = User::inRandomOrder()->take(rand(1, 3))->get();
                $multimedia->multimediaArtists()->attach($users);
            });
    }
}
```