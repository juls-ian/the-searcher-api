<?php

use App\Models\CommunitySegment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('segments_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CommunitySegment::class, 'segment_id')
                ->constrained('community_segments')
                ->onDelete('cascade');
            $table->text('question');
            $table->text('options');
            $table->dateTime('ends_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segments_polls');
    }
};