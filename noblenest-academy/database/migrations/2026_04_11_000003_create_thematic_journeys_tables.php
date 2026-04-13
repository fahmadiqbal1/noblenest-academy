<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thematic journeys (e.g. "The Ocean", "My Body", "Space & Stars")
        Schema::create('thematic_journeys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('age_tier', 50)->comment('baby, toddler, preschool, school');
            $table->string('emoji', 10)->default('🌟');
            $table->string('cover_color', 7)->default('#6C63FF')->comment('Hex color for journey card');
            $table->unsignedTinyInteger('total_weeks')->default(4);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('age_tier');
            $table->index(['age_tier', 'is_published']);
        });

        // Each week within a journey has a theme
        Schema::create('weekly_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_id')->constrained('thematic_journeys')->cascadeOnDelete();
            $table->unsignedTinyInteger('week_number');
            $table->string('theme_name');
            $table->text('theme_description')->nullable();
            $table->string('theme_emoji', 10)->default('📅');
            $table->string('big_idea', 255)->nullable()
                  ->comment('The overarching concept for the week, e.g. "Water connects everything on Earth"');
            $table->timestamps();

            $table->unique(['journey_id', 'week_number']);
        });

        // Each activity slot within a weekly theme (up to 10 per week, one per day AM/PM)
        Schema::create('theme_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_theme_id')->constrained('weekly_themes')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('subject_slot', 100)->comment('Subject context for this slot: Math, Science, Art, etc.');
            $table->unsignedTinyInteger('day_of_week')->comment('1=Mon ... 5=Fri');
            $table->enum('time_of_day', ['morning', 'afternoon'])->default('morning');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['weekly_theme_id', 'day_of_week']);
        });

        // Track which journey each child is currently enrolled in
        Schema::create('child_journey_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained('child_profiles')->cascadeOnDelete();
            $table->foreignId('journey_id')->constrained('thematic_journeys')->cascadeOnDelete();
            $table->unsignedTinyInteger('current_week')->default(1);
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['child_profile_id', 'journey_id']);
            $table->index(['child_profile_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_journey_enrollments');
        Schema::dropIfExists('theme_activities');
        Schema::dropIfExists('weekly_themes');
        Schema::dropIfExists('thematic_journeys');
    }
};
