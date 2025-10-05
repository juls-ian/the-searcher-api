# Unused codes in UserFactory 

## factory 
### 1.0: non-custom email 
```php 
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            // 'full_name' => $firstName . ' ' . $lastName,
            'pen_name' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'course' => fake()->word(),
            'phone' => fake()->phoneNumber(),
            'board_position' => fake()->jobTitle(),
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
```