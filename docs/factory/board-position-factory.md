# Unused factory

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

            // Artists (Editor)
            'Head Artist',
            'Head Graphics and Layout Artist',
            'Head Photojournalist',

            // Writers (Staff)
            'Staff Writer',
            'Staff Reporter',

            // Artists (Staff)
            'Graphics and Layout Artist',
            'Photojournalist',
        ];

        // Clear existing to prevent duplicates (optional but safe)
        BoardPosition::truncate();

        foreach ($positions as $name) {
            BoardPosition::create([
                'name' => $name,
                'category' => $this->determineCategory($name),
            ]);
        }
    }

    /**
     * Determine category automatically based on position name.
     */
    private function determineCategory(string $name): string
    {
        $name = Str::lower($name);

        // Executives
        if (Str::contains($name, [
            'editor-in-chief',
            'managing editor',
            'associate editor',
            'assoc. managing editor',
            'circulation manager',
        ])) {
            return 'executive';
        }

        // Writers (Editor)
        if (Str::contains($name, [
            'copy editor',
            'news editor',
            'feature editor',
            'literary editor',
            'community editor',
            'sports editor',
        ])) {
            return 'writers (editor)';
        }

        // Artists (Editor)
        if (Str::contains($name, [
            'head artist',
            'head graphics',
            'head photojournalist',
        ])) {
            return 'artists (editor)';
        }

        // Writers (Staff)
        if (Str::contains($name, [
            'writer',
            'reporter',
        ])) {
            return 'writers (staff)';
        }

        // Artists (Staff)
        if (Str::contains($name, [
            'artist',
            'layout',
            'photojournalist',
            'illustrator',
        ])) {
            return 'artists (staff)';
        }

        return 'uncategorized';
    }
}
