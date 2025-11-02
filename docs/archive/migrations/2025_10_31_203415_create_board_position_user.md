# Removed migration 

## Reason 
Users can have multiple positions (like Editor AND Photographer at the same time)
Users can serve multiple terms (like 2024-2025, then renew for 2025-2026)
This pivot table is not really needed, this can cause data duplication, and inconsistency. 
The editorial_boards table will suffice, which will contain the multiple positions and terms 

| id | user_id | board_position_id | term      | is_current |
|----|---------|-------------------|-----------|------------|
| 1  | 10      | 5 (Editor)        | 2025-2026 | true       | ← Ian, current term, position 1
| 2  | 10      | 8 (Photographer)  | 2025-2026 | true       | ← Ian, current term, position 2
| 3  | 10      | 5 (Editor)        | 2024-2025 | false      | ← Ian, previous term
| 4  | 15      | 3 (Writer)        | 2025-2026 | true       | ← Jean, current term 

```php
<?php

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
        // Pivot table for many to many relationship
        Schema::create('board_position_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_position_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['board_position_id', 'user_id']); // optional but good
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_position_user');
    }
};
```
