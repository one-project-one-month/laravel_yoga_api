<?php

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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('payment_mathod_id')->constrained('payments');
            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->string('ph_no');
            $table->string('payslip_image_url');
            $table->string('payslip_image_public_id');
            $table->string('transaction_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
