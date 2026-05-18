<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the `published` column the app already depends on.
 *
 * ContentReviewController, ContentBatchController, ProcessContentBatchJob,
 * AIAssistantService and OnboardingController all read/write
 * activities.published, but no migration ever created it — every one of
 * those paths was a latent "Unknown column 'published'" 500 (onboarding
 * step 5 crashed for every new signup).
 *
 * Default is TRUE so existing/seeded curriculum stays visible. AI- and
 * batch-generated activities explicitly set published=false and surface
 * in the admin review queue until approved.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'published')) {
                $table->boolean('published')->default(true)->index()->after('is_free');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'published')) {
                $table->dropColumn('published');
            }
        });
    }
};
