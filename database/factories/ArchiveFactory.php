<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Archive>
 */
class ArchiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(4, 8));
        return [
            'archivable_type' => $this->faker->randomElement([
                'article',
                'multimedia',
                'community-segment',
                'bulletin',
                'issue'
            ]),
            'archivable_id' => $this->faker->numberBetween(1, 100),
            'title' => $title,
            'slug' => Str::slug($title),
            'data' => fn(array $attributes) => $this->generateDataByType($attributes['archivable_type']),
            'archived_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'archiver_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    /**
     * Generate data based on archivable_type
     */
    private function generateDataByType(string $type): array
    {
        switch ($type) {
            case 'article':
                return [
                    'article_category_id' => $this->faker->numberBetween(1, 10),
                    'writer_id' => User::factory()->create()->id,
                    'body' => $this->faker->paragraphs(5, true),
                    'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                    'cover_photo' => $this->faker->imageUrl(800, 600, 'articles'),
                    'cover_artist_id' => User::factory()->create()->id,
                    'credit_type' => $this->faker->randomElement(['photo', 'illustration', 'graphic']),
                ];

            case 'multimedia':
                return [
                    'category' => $this->faker->randomElement(['photography', 'video', 'audio', 'infographic']),
                    'caption' => $this->faker->paragraph(),
                    'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                    'files' => $this->generateMultimediaFiles(),
                    'multimedia_artists_id' => User::factory()->count(rand(1, 3))->create()->pluck('id')->toArray(),
                    'thumbnail' => $this->faker->imageUrl(400, 300, 'thumbnails'),
                    'thumbnail_artist_id' => User::factory()->create()->id,
                    'credit_type' => $this->faker->randomElement(['photo', 'video', 'design']),
                ];

            case 'community-segment':
                return [
                    'writer_id' => User::factory()->create()->id,
                    'series_type' => $this->faker->randomElement(['weekly', 'monthly', 'special']),
                    'series_of' => $this->faker->words(3, true),
                    'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                    'series_order' => $this->faker->numberBetween(1, 20),
                    'segment_cover' => $this->faker->imageUrl(800, 600, 'segments'),
                    'cover_artist_id' => User::factory()->create()->id,
                    'credit_type' => $this->faker->randomElement(['photo', 'illustration']),
                ];

            case 'bulletin':
                return [
                    'writer_id' => User::factory()->create()->id,
                    'category' => $this->faker->randomElement(['announcement', 'news', 'update', 'alert']),
                    'details' => $this->faker->paragraphs(3, true),
                    'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                    'cover_photo' => $this->faker->imageUrl(800, 600, 'bulletins'),
                    'cover_artist_id' => User::factory()->create()->id,
                ];

            case 'issue':
                return [
                    'description' => $this->faker->paragraph(),
                    'published_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
                    'editors' => $this->generateStaffList('editor'),
                    'writers' => $this->generateStaffList('writer'),
                    'photojournalists' => $this->generateStaffList('photographer'),
                    'artists' => $this->generateStaffList('artist'),
                    'layout_artists' => $this->generateStaffList('layout'),
                    'contributors' => $this->generateStaffList('contributor'),
                    'file' => $this->faker->url() . '/issue.pdf',
                    'thumbnail' => $this->faker->imageUrl(400, 600, 'issues'),
                ];

            default:
                return [];
        }
    }

    /**
     * Generate multimedia files array
     */
    private function generateMultimediaFiles(): array
    {
        $fileCount = $this->faker->numberBetween(1, 5);
        $files = [];

        for ($i = 0; $i < $fileCount; $i++) {
            $files[] = [
                'url' => $this->faker->imageUrl(1200, 800, 'media'),
                'type' => $this->faker->randomElement(['image', 'video', 'audio']),
                'size' => $this->faker->numberBetween(1000, 5000000),
                'filename' => $this->faker->word() . '.' . $this->faker->randomElement(['jpg', 'png', 'mp4', 'mp3']),
            ];
        }

        return $files;
    }

    /**
     * Generate staff list for issue credits
     */
    private function generateStaffList(string $role): array
    {
        $count = $this->faker->numberBetween(1, 5);
        $staff = [];

        for ($i = 0; $i < $count; $i++) {
            $staff[] = [
                'id' => User::factory()->create()->id,
                'full_name' => $this->faker->name(),
                'role' => $role,
            ];
        }

        return $staff;
    }

    /**
     * State methods for specific archive types
     */
    public function article()
    {
        return $this->state(fn(array $attributes) => [
            'archivable_type' => 'article',
            'title' => 'Archived Article: ' . $this->faker->sentence(3),
        ]);
    }

    public function multimedia()
    {
        return $this->state(fn(array $attributes) => [
            'archivable_type' => 'multimedia',
            'title' => 'Archived Media: ' . $this->faker->words(3, true),
        ]);
    }

    public function communitySegment()
    {
        return $this->state(fn(array $attributes) => [
            'archivable_type' => 'community-segment',
            'title' => 'Archived Segment: ' . $this->faker->words(3, true),
        ]);
    }

    public function bulletin()
    {
        return $this->state(fn(array $attributes) => [
            'archivable_type' => 'bulletin',
            'title' => 'Archived Bulletin: ' . $this->faker->words(3, true),
        ]);
    }

    public function issue()
    {
        return $this->state(fn(array $attributes) => [
            'archivable_type' => 'issue',
            'title' => 'Issue #' . $this->faker->numberBetween(1, 100),
        ]);
    }

    /**
     * State for recently archived items
     */
    public function recent()
    {
        return $this->state(fn(array $attributes) => [
            'archived_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * State for old archived items
     */
    public function old()
    {
        return $this->state(fn(array $attributes) => [
            'archived_at' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
        ]);
    }
}
