# Unused factory 

## base factory
### 1.0: before moving these out to board position factory
```php
<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\BoardPosition;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BoardPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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

        foreach ($positions as $index => $name) {
            $category = $this->determineCategory($name);

            BoardPosition::create([
                'name' => $name, // field name
                'category' => $category // field category
            ]);
        }
    }

    /**
     * Detect category from position name
     */
    private function determineCategory(string $name)
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

        // Fallback
        logger("Uncategorized position detected: {$name}");
        return 'uncategorized';
    }
}
```
