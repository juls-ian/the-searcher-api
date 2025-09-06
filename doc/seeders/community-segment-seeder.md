# Scrapped codes in CommunitySegmentSeeder

## initial code
class CommunitySegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (User::all() as $user) {

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