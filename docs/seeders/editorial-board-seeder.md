# Scrapped codes in EditorialBoardSeeder

## initial code
```php
class EditorialBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            EditorialBoard::factory()->create([
                'user_id'    => $user->id,
                'term'       => '2025-2026',
                'is_current' => true
            ]);

            $additionalTerms = rand(0, 2);
            for ($i = 0; $i < $additionalTerms; $i++) {
                $startYear = 2020 + $i;
                $endYear   = $startYear + 1;

                EditorialBoard::factory()->create([
                    'user_id'    => $user->id,
                    'term'       => "{$startYear}-{$endYear}",
                    'is_current' => false
                ]);
            }
        }
    }
}
```