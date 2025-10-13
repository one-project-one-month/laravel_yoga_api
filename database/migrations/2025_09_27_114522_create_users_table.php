<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('nick_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->default(3)->references('id')->on('roles')->onDelete('cascade');
            $table->string('profile_url')->nullable();
            $table->string('profile_public_id')->nullable();
            $table->string('ph_no_telegram')->nullable();
            $table->string('ph_no_whatsapp')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->text('daily_routine_for_weekly')->nullable();
            $table->text('special_request')->nullable();
            $table->string('weight')->nullable();
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
