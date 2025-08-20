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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('event_type', ['release', 'event', 'meeting'])->default('event');
            $table->dateTime('start_at');
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_allday')->default(false);
            $table->string('venue')->nullable();
            $table->string('details')->nullable();
            $table->boolean('is_public')->default(true);
            // removed the column, will be handled in the model instead
            // $table->enum('status', ['upcoming', 'happening', 'concluded'])->default('upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
