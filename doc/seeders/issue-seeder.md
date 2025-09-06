# Scrapped codes in IssueSeeder 
*similar code applies to bulletin, calendar, and article category seeders

# initial code 
class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Issue::factory()->count(10)->create();
    }
}
