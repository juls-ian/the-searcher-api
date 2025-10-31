# Unused codes in the migration

## Reason of this migration:
Since i'm the sole of the api, i simply removed the multimedia_artists_id from the multimedia migration even though it's already been run 

### Note:
However, for future developments or when working when a team migration like this is a must because if the multimedia migration has been run:
- The migration is already recorded in your migrations table
- Other developers or environments (staging, production) have already applied it
- Modifying it won't affect your current database
- It could cause issues when deploying to other environments 

## Code 
```php 
<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('multimedia', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['multimedia_artists_id']);
            // Then drop the column
            $table->dropColumn('multimedia_artists_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multimedia', function (Blueprint $table) {
            Schema::table('multimedia', function (Blueprint $table) {
                // Recreate the column and foreign key if rollback is needed
                $table->foreignIdFor(User::class, 'multimedia_artists_id')
                    ->constrained('users');
            });
        });
    }
};
```
