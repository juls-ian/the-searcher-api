<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Issue>
 */
class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'description' => $this->faker->paragraph(),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'editors' => $this->faker->word(3),
            'writers' => $this->faker->words(4),
            'photojournalists' => $this->faker->words(2),
            'artists' => $this->faker->words(2),
            'layout_artists' => $this->faker->words(2),
            'contributors' => $this->faker->words(3),
            'issue_file' => 'issues/' . $this->faker->uuid . '.pdf',
            'thumbnail' => 'thumbnails/' . $this->faker->uuid . '.jpg',
        ];
    }
}
