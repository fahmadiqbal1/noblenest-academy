<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->date('delivery_date')->nullable();
            $table->unsignedTinyInteger('birth_count')->default(0);
            $table->string('delivery_preference', 20)->default('undecided');
            $table->text('health_conditions_encrypted')->nullable();
            $table->text('dietary_restrictions_encrypted')->nullable();
            $table->string('baby_feeding_method', 20)->default('undecided');
            $table->string('preferred_language', 8)->default('en');
            $table->boolean('is_muslim')->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->string('status', 20)->default('active');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->json('notification_preferences')->nullable();
            $table->timestamp('consent_accepted_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_profiles');
    }
};
