<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lesson_trainer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users');
            $table->foreignId('lesson_type_id')->constrained('lesson_types');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lesson_trainer');
    }
};
