# Scrapped codes in the BulletinFactory 

## v.1
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bulletin>
 */
class BulletinFactory extends Factory
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
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'category' => fake()->randomElement(['advisory', 'announcement']),
            'writer_id' => User::factory(),
            'details' => fake()->paragraph(),
            'cover_photo' => function () {
                $directory = storage_path('app/public/images/bulletin/covers');

                // Create directory if it doesn't exist
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                return fake()->image(
                    dir: $directory,
                    width: 640,
                    height: 480,
                    category: 'nature',
                    fullPath: true
                );
            },
            'cover_artist_id' => User::factory()

        ];
    }
}
