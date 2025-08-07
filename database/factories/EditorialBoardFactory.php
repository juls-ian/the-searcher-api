<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EditorialBoard>
 */
class EditorialBoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $startYear = $this->faker->numberBetween(2020, 2030);
        $endYear = $startYear + 1;

        return [
            'user_id' => User::factory(),
            'term' => "{$startYear}-{$endYear}",
            'is_current' => $this->faker->boolean(30) # 30% chance
        ];
    }

    /**
     * Create active ed board member
     */
    public function inactive()
    {
        return $this->state(fn(array $attributes) => [
            'is_current' => true
        ]);
    }

    /**
     * Create an ed board member for a specific term.
     */
    public function forTerm(string $term): static
    {
        return $this->state(fn(array $attributes) => [
            'term' => $term,
        ]);
    }
    /**
     * Create an ed board member for the current academic year.
     */
    public function currentTerm()
    {
        $currentYear = now()->year;
        // If we're in the second half of the year, use current year as start
        // Otherwise, use previous year as start
        $startYear = now()->month >= 8 ? $currentYear : $currentYear - 1;
        $endYear = $startYear + 1;

        return $this->state(fn(array $attributes) => [
            'term' => "{$startYear}-{$endYear}",
            'is_current' => true,
        ]);
    }
}
