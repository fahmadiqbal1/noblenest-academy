<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates child_profiles table separated from users for COPPA compliance.
     * Children do not have login credentials - only parents can access their profiles.
     */
    public function up(): void
    {
        Schema::create('child_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('preferred_language', 8)->default('en');
            $table->string('avatar_url')->nullable();
            $table->json('preferences')->nullable(); // UI preferences, themes, etc.
            $table->json('learning_goals')->nullable(); // Parent-set learning goals
            $table->timestamps();

            $table->index('parent_id');
            $table->index('date_of_birth');
            $table->index('preferred_language');
        });

        // Progress tracking for child activities (separate from user progress)
        Schema::create('child_activity_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->unsignedTinyInteger('score')->nullable(); // 0-100
            $table->unsignedSmallInteger('time_spent')->nullable(); // seconds
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->json('trace_data')->nullable(); // For tracing activities
            $table->string('drawing_path')->nullable(); // Storage path for saved drawings
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['child_profile_id', 'activity_id']);
            $table->index('status');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_activity_progress');
        Schema::dropIfExists('child_profiles');
    }
};
