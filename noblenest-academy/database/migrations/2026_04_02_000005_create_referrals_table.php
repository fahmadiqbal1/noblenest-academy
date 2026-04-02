<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code', 20)->unique();
            $table->enum('status', ['pending', 'signed_up', 'subscribed'])->default('pending');
            $table->timestamp('signed_up_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->decimal('reward_amount', 8, 2)->default(0.00); // credit awarded
            $table->boolean('reward_issued')->default(false);
            $table->timestamps();
        });

        // Add referral_code to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->unique()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
        Schema::dropIfExists('referrals');
    }
};
