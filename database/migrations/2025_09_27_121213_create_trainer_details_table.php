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
            $table->string('university_name');
            $table->string('degree');
            $table->string('city');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('branch_location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('trainer_details');
    }
};
