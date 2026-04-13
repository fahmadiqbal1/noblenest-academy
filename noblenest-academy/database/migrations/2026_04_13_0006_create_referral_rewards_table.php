<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->unique()->constrained('referrals')->cascadeOnDelete();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tier', ['tier1', 'tier2', 'tier3']);
            $table->enum('reward_type', ['stripe_coupon', 'credit_days']);
            $table->string('stripe_coupon_id')->nullable();
            $table->char('currency', 3)->nullable();
            $table->smallInteger('granted_days')->default(0);
            $table->enum('status', ['pending', 'issued', 'consumed', 'expired', 'failed'])->default('pending');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['referrer_user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
