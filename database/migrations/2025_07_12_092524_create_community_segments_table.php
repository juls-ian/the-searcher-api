<?php

use App\Models\CommunitySegment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_segments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('segment_type', ['article', 'poll']);
            $table->enum('series_type', ['standalone', 'series_header', 'series_issue'])->default('standalone');
            $table->foreignIdFor(User::class, 'writer_id')
                ->constrained('users');
            $table->foreignIdFor(CommunitySegment::class, 'series_of')
                ->nullable()
                ->constrained('community_segments')
                ->onDelete('set null');
            $table->dateTimeTz('published_at');
            $table->integer('series_order')->nullable();
            $table->string('segment_cover');
            $table->enum('credit_type', ['photo', 'graphics'])->default('photo');
            $table->foreignIdFor(User::class, 'cover_artist_id');
            $table->string('cover_caption')->nullable();
            $table->foreignIdFor(User::class, 'publisher_id')
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_segments');
    }
};
