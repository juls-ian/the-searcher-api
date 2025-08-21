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
        Schema::create('multimedia', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['gallery', 'video', 'illustration', 'segment']);
            $table->string('caption');
            $table->dateTimeTz('published_at');
            $table->string('files');
            $table->string('thumbnail');
            $table->foreignIdFor(User::class, 'thumbnail_artist_id')
                ->constrained('users');
            $table->foreignIdFor(User::class, 'publisher_id')
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            /**
             * needs to be dropped for the m:m pivot table which will cause an error 
             * direct foreign key is redundant and conflicting since we already have a pivot table 
             */
            // $table->foreignIdFor(User::class, 'multimedia_artists_id')
            //     ->constrained('users'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multimedia');
    }
};
