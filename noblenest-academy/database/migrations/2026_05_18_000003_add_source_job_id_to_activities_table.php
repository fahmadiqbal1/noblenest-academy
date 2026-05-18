<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds activities.source_job_id (C7).
 *
 * ProcessContentBatchJob and AIAssistantService write 'source_job_id' when
 * creating AI-generated activities, but the column never existed AND it was
 * not in Activity::$fillable — so the link was silently dropped on create.
 * ContentBatchController::preview()/publish() then query
 * `Activity::where('source_job_id', $job->id)` → "Unknown column" 500. The
 * entire admin AI content-batch preview/publish pipeline was dead.
 *
 * Nullable + indexed; FK to ai_jobs is intentionally loose (set null on
 * delete) so purging a finished job never cascades away published content.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'source_job_id')) {
                $table->foreignId('source_job_id')
                    ->nullable()
                    ->after('published')
                    ->constrained('ai_jobs')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'source_job_id')) {
                $table->dropConstrainedForeignId('source_job_id');
            }
        });
    }
};
