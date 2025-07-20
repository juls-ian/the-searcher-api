<?php

namespace Database\Factories;

use App\Models\CommunitySegment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SegmentsPoll>
 */
class SegmentsPollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $options = collect(range(1, fake()->numberBetween(2, 5)))
            ->map(fn() => fake()->sentence(3))
            ->toArray();
        return [
            'segment_id' => CommunitySegment::factory()->poll(),
            'question' => fake()->sentence() . '?',
            'options' => $options,
            'ends_at' => fake()->dateTimeBetween('+1 day', '+1 month')
        ];
    }

    public function forSegment(CommunitySegment $segment): static
    {
        return $this->state(fn(array $attributes) => [
            'segment_id' => $segment->id,
        ]);
    }

    public function withOptions(array $options): static
    {
        return $this->state(fn(array $attributes) => [
            'options' => json_encode($options),
        ]);
    }

    public function endingIn(string $time): static
    {
        return $this->state(fn(array $attributes) => [
            'ends_at' => now()->modify($time),
        ]);
    }
}