<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calendar>
 */
class CalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'start_at' => fake()->dateTimeBetween('now', '+6 days'),
            'ends_at' => fake()->optional(0.9)->dateTimeBetween('now', '+7 days'),
            'is_allday' => fake()->boolean(20),
            'venue' => fake()->city() . ',' . ' ' . fake()->country(),
            'details' => fake()->sentence(8, true),
            'event_type' => fake()->randomElement(['event', 'meeting', 'release'])
        ];
    }
}
