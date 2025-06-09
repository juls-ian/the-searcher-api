<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Article;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // $this->call(ArticleCategorySeeder::class);
        ArticleCategory::factory()->createCompleteStructure();


        $users = [
            User::factory()->create([
                'first_name' => 'Ian',
                'last_name' => 'Valdez',
                'email' => 'ian@email.com'
            ]),

            User::factory()->create([
                'first_name' => 'Rod',
                'last_name' => 'Bacason',
                'email' => 'rod@email.com'
            ])
        ];

        foreach ($users as $user) {
            Article::factory(2)->create([
                'writer_id' => $user->id
            ]);
        }
    }
}