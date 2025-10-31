<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('appointment_type');
            $table->decimal('appointment_fees', 10, 2)->default("0");
            $table->string('meet_link')->nullable();
            $table->string('is_approved')->default("pending");
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('appointments');
    }
};
