<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('paypal_order_id')->unique();
            $table->string('paypal_capture_id')->nullable();
            $table->string('plan');
            $table->integer('amount');
            $table->char('currency', 3);
            $table->enum('status', ['created', 'approved', 'completed', 'voided', 'failed'])->default('created');
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_transactions');
    }
};
