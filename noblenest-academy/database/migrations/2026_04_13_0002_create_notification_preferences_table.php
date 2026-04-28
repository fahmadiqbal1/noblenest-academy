<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('streak_warning')->default(true);
            $table->boolean('struggle_alert')->default(true);
            $table->boolean('milestone')->default(true);
            $table->boolean('reengagement')->default(true);
            $table->boolean('marketing')->default(false);
            $table->enum('digest_frequency', ['daily', 'weekly', 'off'])->default('daily');
            $table->boolean('sms_streak_warning')->default(false);
            $table->boolean('sms_payment_failed')->default(true);
            $table->boolean('sms_milestone')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
