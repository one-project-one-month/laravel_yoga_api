<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trainer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users');
            $table->text('bio');
            $table->text('description');
            $table->decimal('salary', 10, 2);
            $table->string('branch_location');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('trainer_details');
    }
};
