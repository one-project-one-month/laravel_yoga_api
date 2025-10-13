<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('level', ['beginner','intermediate','advanced']);
            $table->string('video_url');
            $table->string('video_public_id')->nullable();
            $table->foreignId('lesson_type_id')->constrained('lesson_types');
            $table->integer('duration_minutes');
            $table->boolean('is_free')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->foreignId('trainer_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lessons');
    }
};
