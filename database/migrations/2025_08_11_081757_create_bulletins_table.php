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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['advisory', 'announcement']);
            $table->foreignIdFor(User::class, 'writer_id')
                ->constrained('users');
            $table->longText('details');
            $table->dateTimeTz('published_at');
            $table->string('cover_photo');
            $table->foreignIdFor(User::class, 'cover_artist_id')
                ->constrained('users');
            $table->foreignIdFor(User::class, 'publisher_id')
                ->constrained('users');
            $table->timestampTz('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
