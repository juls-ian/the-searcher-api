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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->dateTimeTz('published_at');
            $table->json('editors');
            $table->json('writers');
            $table->json('photojournalists');
            $table->json('artists');
            $table->json('layout_artists');
            $table->json('contributors')->nullable();
            $table->string('file');
            $table->string('thumbnail');
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
        Schema::dropIfExists('issues');
    }
};
