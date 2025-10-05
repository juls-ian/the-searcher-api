# Scrapped codes in BulletinSeeder

## initial code
```php
class BulletinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            Bulletin::factory(2)->create([
                'writer_id' => $user->id
            ]);
        });
    }
}
```