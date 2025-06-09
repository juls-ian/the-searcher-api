<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
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
            'category_id' => ArticleCategory::inRandomOrder()->first()->id, // assign to existing category
            'writer_id' => User::factory(),
            'body' => fake()->paragraphs(rand(3, 8), true),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_live' => fake()->boolean(30), // 30% chance of being live
            'is_header' => fake()->boolean(10), // 10% chance of being header
            'series_id' => null, // Will be set conditionally
            'is_archived' => fake()->boolean(20), // 20% chance of being archived
            'cover_photo' => fake()->imageUrl(800, 600, 'news'),
            // 'cover_photo' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            'cover_caption' => fake()->optional(0.9)->sentence(),
            'cover_artist_id' => User::factory(),
            'thumbnail' => fake()->imageUrl(300, 200, 'news'),
            // 'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg', 800, 600),
            'thumbnail_artist_id' => User::factory(),
            'archived_at' => fake()->optional(0.2)->dateTimeBetween('-1 year', 'now'),
            'add_to_ticker' => fake()->boolean(10),
            'ticker_expires_at' => fake()->dateTimeBetween('now', '1 week'),

        ];
    }

    /**
     * Make the article live/published
     */
    public function live(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_live' => true,
            'published_on' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Make the article a header article
     */
    public function header(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_live' => true,
            'is_header' => true,
            'published_on' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Make the article part of a series
     */
    public function inSeries($seriesId = null): static
    {
        return $this->state(fn(array $attributes) => [
            'series_id' => $seriesId ?? Article::factory()->create()->id,
            'is_live' => true,
        ]);
    }

    /**
     * Make the article archived
     */
    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
            'archived_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }

    /**
     * Make the article a draft (not live)
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_live' => false,
            'published_on' => fake()->dateTimeBetween('now', '+1 month'),
        ]);
    }

    /**
     * Make the article without cover photo
     */
    public function withoutCover(): static
    {
        return $this->state(fn(array $attributes) => [
            'cover_photo' => null,
            'cover_caption' => null,
        ]);
    }

    /**
     * Make the article without thumbnail
     */
    public function withoutThumbnail(): static
    {
        return $this->state(fn(array $attributes) => [
            'thumbnail' => null,
            'thumbnail_artist' => null,
        ]);
    }
}