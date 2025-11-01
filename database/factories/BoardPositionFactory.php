<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\BoardPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoardPosition>
 */
class BoardPositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>  'Placeholder',
            'category' => 'uncategorized'
        ];
    }

    /**
     * Seeds the database with only the fixed board positions.
     */
    public function createDefaultPositions(): void
    {
        $positions = [
            // Executives
            'Editor-in-Chief',
            'Managing Editor',
            'Associate Editor',
            'Assoc. Managing Editor',
            'Circulation Manager',
            'Asst. Circulation Manager',

            // Writers (Editor)
            'Copy Editor',
            'News Editor',
            'Feature Editor',
            'Literary Editor',
            'Community Editor',
            'Sports Editor',
            'Opinion Editor',

            // Artists (Editor)
            'Head Artist',
            'Head Graphics and Layout Artist',
            'Head Photojournalist',
            'Head Videographer',
            'Chief Illustrator',
            'Chief Media Artist',

            // Writers (Staff)
            'Staff Writer',

            // Artists (Staff)
            'Graphics and Layout Artist',
            'Photojournalist',
            'Senior Illustrator',
            'Senior Photojournalist',
            'Senior Media Artist',


        ];

        // Delete all existing records before creating new ones
        BoardPosition::truncate();

        foreach ($positions as $name) {
            BoardPosition::create([
                'name' => $name,
                'category' => BoardPosition::determineCategory($name),
            ]);
        }
    }
}
