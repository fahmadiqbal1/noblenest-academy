<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 — discovery battery (assessment) tables.
 *
 * `assessment_questions` — the static question bank (30 questions seeded by
 *   AssessmentBatterySeeder). Each question maps an answer to a "dimension"
 *   score: cognitive_logic, creative, social, kinetic, naturalist, linguistic.
 *
 * `assessment_responses` — one row per completed battery per child. Stores the
 *   raw answer vector + computed per-dimension scores. Soft-deletes preserved so
 *   the parent's history is auditable for the COPPA-safe PDF report.
 *
 * Framed as an "interest indicator" — NOT clinical (master-prompt step 6).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->string('battery', 32)->default('discovery');     // future: 'iq', 'big-five-mini'
            $table->unsignedSmallInteger('sequence');                // 1..30 order
            $table->string('age_min_months')->nullable();            // optional age gate
            $table->string('age_max_months')->nullable();
            $table->text('prompt');
            $table->json('options');                                 // ['label' => 'Building things', 'dimensions' => ['kinetic' => 2, 'creative' => 1]]
            $table->timestamps();

            $table->unique(['battery', 'sequence']);
        });

        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->nullable()->constrained('child_profiles')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('battery', 32)->default('discovery');
            $table->json('answers');                                 // [{sequence: 1, option: 2}, ...]
            $table->json('scores');                                  // {cognitive_logic: 12, creative: 8, ...}
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['child_id', 'battery']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_responses');
        Schema::dropIfExists('assessment_questions');
    }
};
