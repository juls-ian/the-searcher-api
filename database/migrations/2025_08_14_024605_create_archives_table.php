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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('archivable_type'); # models
            $table->unsignedBigInteger('archivable_id')
                ->nullable(); # record's primary key 
            $table->string('title');
            $table->string('slug');
            $table->json('data');
            $table->dateTimeTz('archived_at');
            $table->foreignIdFor(User::class, 'archiver_id')
                ->constrained('users')
                ->onDelete('restrict'); # prevent deletion of user if they have related records 
            $table->timestamps();
            $table->softDeletes();

            $table->index(['archivable_type', 'archivable_id']); # indexes for better performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
