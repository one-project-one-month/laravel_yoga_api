<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('avatar')->nullable();
            $table->string('ph_no')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_first_time_appointment')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
