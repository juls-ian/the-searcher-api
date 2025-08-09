<?php

use App\Models\ArticleCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            // define FK parent_id
            $table->foreignIdFor(ArticleCategory::class, 'parent_id')
                ->nullable()  // because onDelete('set null') implies the column can be null
                ->constrained('article_categories')
                ->onDelete('set null'); // set parent_id to NULL if the parent category is deleted
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_categories');
    }
};
