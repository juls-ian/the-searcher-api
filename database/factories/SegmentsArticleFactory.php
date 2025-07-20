<?php

namespace Database\Factories;

use App\Models\CommunitySegment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SegmentsArticle>
 */
class SegmentsArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'segment_id' => CommunitySegment::factory()->article(),
            'body' => fake()->paragraphs(4, true)
        ];
    }

    public function forSegment(CommunitySegment $segment)
    {
        return $this->state(fn(array $attributes) => [
            'segment_id' => $segment->id
        ]);
    }
}