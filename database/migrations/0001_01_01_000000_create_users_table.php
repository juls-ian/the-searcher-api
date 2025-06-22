<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name', 200)->storedAs("first_name || ' ' || last_name")->nullable();
            $table->string('full_name_slug')->unique();
            $table->string('pen_name');
            $table->string('pen_name_slug')->unique();
            $table->string('staff_id', 100)->unique();
            $table->string('email')->unique();
            $table->string('year_level');
            $table->string('course');
            $table->string('phone');
            $table->string('board_position');
            $table->enum('role', ['admin', 'editor', 'staff'])->default('staff');
            $table->string('term');
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active');
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->string('profile_pic');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // soft delete feature deleted_at column

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};