<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Child Skill State: Rolling projection of mastery per child, cognitive domain, developmental domain.
         * Updated on every ActivityCompleted event.
         * Used by LearningPathService to adapt next activity selection.
         */
        Schema::create('child_skill_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained('child_profiles')->cascadeOnDelete();
            $table->string('cognitive_domain', 100)
                  ->comment('math, language, science, art, music, executive_function, emotional_regulation, social_emotional, physical_development');
            $table->string('developmental_domain', 100)
                  ->comment('gross_motor, fine_motor, language, cognitive, social_emotional, self_care, attention, memory, reasoning');

            // EMA-style rolling score (0.0 - 1.0)
            $table->decimal('ema_score', 5, 3)->default(0.5)
                  ->comment('Exponential moving average of mastery score. 0.5 = unknown, >0.8 = mastered, <0.5 = struggling');
            $table->decimal('ema_confidence', 5, 3)->default(0.0)
                  ->comment('How confident we are in ema_score (0.0 = no data, 1.0 = high confidence)');

            // Streak tracking (used for rule triggers)
            $table->unsignedTinyInteger('streak_success')->default(0)
                  ->comment('Consecutive successful completions (increments on success, resets on fail)');
            $table->unsignedTinyInteger('streak_struggle')->default(0)
                  ->comment('Consecutive failed/low-mastery attempts (increments on fail, resets on success)');
            $table->unsignedTinyInteger('max_streak_struggle')->default(0)
                  ->comment('Highest struggle streak recorded (for analytics)');

            // Timestamps & tracking
            $table->timestamp('last_updated')->useCurrent()
                  ->comment('When this skill state was last updated');
            $table->timestamp('last_success')->nullable()
                  ->comment('When child last succeeded on this domain');
            $table->timestamp('last_struggle')->nullable()
                  ->comment('When child last struggled on this domain');

            // Metadata
            $table->unsignedInteger('total_attempts')->default(0)
                  ->comment('Total activity attempts on this cognitive/dev domain combo');
            $table->unsignedInteger('successful_attempts')->default(0)
                  ->comment('Successful attempts (score >= mastery threshold)');

            $table->timestamps();

            // Indices for fast lookup
            // Explicit index names — MariaDB has a 64-char identifier limit.
            $table->unique(
                ['child_profile_id', 'cognitive_domain', 'developmental_domain'],
                'css_child_cog_dev_unique'
            );
            $table->index(['child_profile_id', 'ema_score'], 'css_child_ema_idx');
            $table->index(['child_profile_id', 'streak_struggle'], 'css_child_streak_idx');
            $table->index('last_updated', 'css_last_updated_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_skill_states');
    }
};
