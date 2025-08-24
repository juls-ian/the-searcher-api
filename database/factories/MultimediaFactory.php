<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Multimedia>
 */
class MultimediaFactory extends Factory
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
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => fake()->randomElement(['gallery', 'video', 'illustration', 'segment']),
            'caption' => fake()->sentence(6, true),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'files' => fake()->imageUrl(300, 400, 'feature'),
            // 'multimedia_artists_id' => User::factory(),
            'thumbnail' => fake()->imageUrl(300, 400, 'feature'),
            'thumbnail_artist_id' => User::factory(),
            'thumbnail_credit_type' => fake()->randomElement(['photo', 'graphics', 'illustration', 'video']),
            'publisher_id' => User::factory()
        ];
    }
}
