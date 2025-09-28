<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('admin_id')->constrained('users');
            $table->foreignId('trainer_id')->constrained('users');
            $table->dateTime('appointment_date');
            $table->decimal('appointment_fees', 10, 2);
            $table->string('meet_link');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('appointments');
    }
};
