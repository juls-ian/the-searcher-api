# Scrapped codes in ArticleSeeder

## initial code
class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            Article::factory(2)->create([
                'writer_id' => $user->id
            ]);
        });
    }
}