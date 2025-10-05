<?php

namespace Database\Seeders;

use App\Http\Resources\SegmentArticleResource;
use App\Models\Archive;
use App\Models\User;
use App\Models\Article;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ArticleCategory;
use App\Models\Bulletin;
use App\Models\Calendar;
use App\Models\CommunitySegment;
use App\Models\EditorialBoard;
use App\Models\Issue;
use App\Models\Multimedia;
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
        Issue::factory()->count(10)->create();
        Calendar::factory()->count(8)->create();
        Archive::factory()->count(30)->create();

        $users = [
            User::factory()->create([
                'first_name' => 'Ian',
                'last_name' => 'Valdez',
                'email' => 'ianvaldez@iskolarngbayan.pup.edu.ph',
                'role' => 'admin'
            ]),

            User::factory()->create([
                'first_name' => 'Jean',
                'last_name' => 'Grey',
                'email' => 'jeangrey@iskolarngbayan.pup.edu.ph',
                'role' => 'admin'
            ]),

            User::factory()->create([
                'first_name' => 'Rod',
                'last_name' => 'Bacason',
                'email' => 'rodbacason@iskolarngbayan.pup.edu.ph',
                'role' => 'staff'
            ]),

            User::factory()->create([
                'first_name' => 'Scott',
                'last_name' => 'Summers',
                'email' => 'scottsummers@iskolarngbayan.pup.edu.ph',
                'role' => 'editor'
            ]),

            User::factory()->create([
                'first_name' => 'Steve',
                'last_name' => 'Rogers',
                'email' => 'steverogers@iskolarngbayan.pup.edu.ph',
                'role' => 'editor'
            ]),
        ];

        foreach ($users as $user) {

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


            Article::factory(2)->create([
                'writer_id' => $user->id
            ]);

            Bulletin::factory(2)->create([
                'writer_id' => $user->id
            ]);

            // Multimedia::factory(3)->create([
            //     'multimedia_artist_id' => $user->id
            // ]);
        }

        Multimedia::factory()
            ->count(10)
            ->create()
            ->each(function ($multimedia) {
                $users = User::inRandomOrder()->take(rand(1, 3))->get();
                $multimedia->multimediaArtists()->attach($users);
            });

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
