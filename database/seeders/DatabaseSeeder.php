<?php

namespace Database\Seeders;

use App\Http\Resources\SegmentArticleResource;
use App\Models\User;
use App\Models\Article;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ArticleCategory;
use App\Models\CommunitySegment;
use App\Models\SegmentsArticle;
use App\Models\SegmentsPoll;
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
                'email' => 'ian@email.com',
                'role' => 'admin'
            ]),

            User::factory()->create([
                'first_name' => 'Rod',
                'last_name' => 'Bacason',
                'email' => 'rod@email.com',
                'role' => 'editor'
            ])
        ];

        foreach ($users as $user) {
            Article::factory(2)->create([
                'writer_id' => $user->id
            ]);
        }

        // Seeding community segments 
        $this->seedCommunitySegments($users);
    }

    private function seedCommunitySegments(array $users)
    {
        foreach ($users as $user) {

            // Parent segments
            $parentSeries = CommunitySegment::factory()
                ->article()
                ->afterCreating(function (CommunitySegment $segment) {
                    SegmentsArticle::factory()->forSegment($segment)->create();
                })
                ->create([
                    'writer_id' => $user->id,
                    'cover_artist_id' => $user->id,
                    'title' => 'Existing Series: ' . fake()->sentence(3)
                ]);

            // Article segments within a series 
            CommunitySegment::factory(2)
                ->article()
                ->inSeries($parentSeries)
                ->afterCreating(function (CommunitySegment $segment) {
                    SegmentsArticle::factory()->forSegment($segment)->create();
                })
                ->create([
                    'writer_id' => $user->id,
                    'cover_artist_id' => $user->id,
                ]);


            // Article segments in new series 
            CommunitySegment::factory(3)
                ->article()
                ->inNewSeries()
                ->afterCreating(function (CommunitySegment $segment) {
                    SegmentsArticle::factory()->forSegment($segment)->create();
                })
                ->create([
                    'writer_id' => $user->id,
                    'cover_artist_id' => $user->id
                ]);

            // Standalone article segments 
            CommunitySegment::factory(2)
                ->article()
                ->standalone()
                ->afterCreating(function (CommunitySegment $segment) {
                    SegmentsArticle::factory()->forSegment($segment)->create();
                })
                ->create([
                    'writer_id' => $user->id,
                    'cover_artist_id' => $user->id,
                ]);


            // Poll  segments 
            CommunitySegment::factory(3)
                ->poll()
                ->afterCreating(function (CommunitySegment $segment) {
                    SegmentsPoll::factory()->forSegment($segment)->create();
                })
                ->create([
                    'writer_id' => $user->id,
                    'cover_artist_id' => $user->id
                ]);

        }
    }
}