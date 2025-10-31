# Removed migration 

## Reason of this migration:
Since the User and BoardPosition model changed from 1:M to M:M adding a foreign key to the user table 'board_position_id' will no longer be needed 

## Code
```php
<?php

use App\Models\BoardPosition;
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(BoardPosition::class)
                ->constrained('board_positions') // must be pluralized
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['board_position_id']);
            // Then drop the column
            $table->dropColumn('board_position_id');
        });
    }
};
```
