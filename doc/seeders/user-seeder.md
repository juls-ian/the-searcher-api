# Scrapped codes from UserSeeder

## initial code
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Ian',
            'last_name' => 'Valdez',
            'email' => 'ian@email.com',
            'role' => 'admin'
        ]);

        User::factory()->create([
            'first_name' => 'Jean',
            'last_name' => 'Grey',
            'email' => 'jean@email.com',
            'role' => 'admin'
        ]);

        User::factory()->create([
            'first_name' => 'Rod',
            'last_name' => 'Bacason',
            'email' => 'rod@email.com',
            'role' => 'staff'
        ]);

        User::factory()->create([
            'first_name' => 'Scott',
            'last_name' => 'Summers',
            'email' => 'scott@email.com',
            'role' => 'editor'
        ]);

        User::factory()->create([
            'first_name' => 'Steve',
            'last_name' => 'Rogers',
            'email' => 'steve@email.com',
            'role' => 'editor'
        ]);

        // Optional users 
        User::factory(6)->create();
    }
}
