<?php

use App\Models\Article;
use App\Models\ArticleCategory;
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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignIdFor(User::class, 'writer_id')
                ->constrained('users');
            $table->foreignIdFor(ArticleCategory::class, 'category_id')
                ->constrained('article_categories')
                ->onDelete('cascade');
            $table->longText('body');
            $table->timestampTz('published_at');
            $table->boolean('is_live')->default(false);
            $table->boolean('is_header')->default(false); // only for live news
            $table->foreignIdFor(Article::class, 'series_id') // only for live news
                ->nullable()
                ->constrained('articles')
                ->onDelete('set null');
            $table->boolean('is_archived')->default(false);
            $table->string('cover_photo'); // filename
            $table->text('cover_caption')->nullable();
            $table->foreignIdFor(User::class, 'cover_artist_id')
                ->constrained('users');
            $table->boolean('thumbnail_same_as_cover')->default(false);
            $table->string('thumbnail')->nullable(); // filename
            $table->text('thumbnail_caption')->nullable();
            $table->foreignIdFor(User::class, 'thumbnail_artist_id')
                ->constrained('users');
            $table->timestampTz('archived_at')->nullable();
            $table->boolean('add_to_ticker')->default(false);
            $table->timestamp('ticker_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // soft delete feature deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};