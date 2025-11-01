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
        Schema::table('editorial_boards', function (Blueprint $table) {
            $table->foreignId('board_position_id')
                ->after('user_id')
                ->constrained()
                ->onDelete('cascade');

            // A user can hold the same position only once per term
            $table->unique(['user_id', 'board_position_id', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('editorial_boards', function (Blueprint $table) {
            $table->dropForeign(['board_position_id']);
            $table->dropUnique(['user_id', 'board_position_id', 'term']);
            $table->dropColumn('board_position_id');
        });
    }
};
