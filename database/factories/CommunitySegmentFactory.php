<?php

namespace Database\Factories;

use App\Models\CommunitySegment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunitySegment>
 */
class CommunitySegmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $title = fake()->sentence(rand(4, 10));
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(6),
            'segment_type' => fake()->randomElement(['poll', 'article']),
            'writer_id' => User::factory(),
            'series_of' => null,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'series_order' => fake()->numberBetween(1, 9),
            'segment_cover' => fake()->imageUrl(800, 600, 'news'),
            'cover_artist_id' => User::factory(),
            'cover_caption' => fake()->sentence()
        ];

    }

    public function article()
    {
        return $this->state(fn(array $attributes) => [
            'segment_type' => 'article'
        ]);
    }

    public function poll()
    {
        return $this->state(fn(array $attributes) => [
            'segment_type' => 'poll'
        ]);
    }

    public function inSeries(CommunitySegment $parent)
    {
        return $this->afterMaking(function (CommunitySegment $segment) use ($parent) {
            $segment->series_of = $parent->id;
            $segment->series_order = fake()->numberBetween(1, 10);
        });
    }

    public function inNewSeries()
    {
        return $this->state(fn(array $attributes) => [
            'series_of' => CommunitySegment::factory(),
            'series_order' => fake()->numberBetween(1, 10)
        ]);
    }

    public function standalone()
    {
        return $this->state(fn(array $attributes) => [
            'series_of' => null,
            'series_order' => null
        ]);
    }
}