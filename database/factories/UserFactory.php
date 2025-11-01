<?php

namespace Database\Factories;

use App\Models\BoardPosition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $baseEmail = strtolower($firstName . $lastName) . '@iskolarngbayan.pup.edu.ph';

        $email = $baseEmail;
        $counter = 1;

        // Ensuring email uniqueness
        while (User::where('email', $email)->exists()) {
            $email = strtolower($firstName . $lastName . $counter) . '@iskolarngbayan.pup.edu.ph';
            $counter++;
        };

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            // 'full_name' => $firstName . ' ' . $lastName,
            'pen_name' => fake()->userName(),
            'email' => $email,
            'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'course' => fake()->word(),
            'phone' => fake()->phoneNumber(),
            // 'board_position_id' => BoardPosition::inRandomOrder()->value('id'), -- removed due to pivot table
            'role' => fake()->randomElement(['admin', 'editor', 'staff']),
            // 'term' => fake()->word(),
            'status' => fake()->randomElement(['active', 'inactive', 'alumni']),
            'joined_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'left_at' => fake()->boolean(20) ? fake()->dateTimeBetween('-1 year', 'now') : null,
            'profile_pic' => fake()->imageUrl(640, 480, 'people', true),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
