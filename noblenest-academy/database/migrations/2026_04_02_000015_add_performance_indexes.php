<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Performance indexes on hot query paths
        Schema::table('activities', function (Blueprint $table) {
            $table->index(['age_min', 'age_max', 'language'], 'activities_age_lang_idx');
            $table->index(['age_tier', 'is_free', 'language'], 'activities_tier_free_lang_idx');
            $table->index(['skill', 'language'], 'activities_skill_lang_idx');
        });

        Schema::table('child_activity_progress', function (Blueprint $table) {
            $table->index(['child_profile_id', 'completed_at'], 'cap_child_completed_idx');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'active', 'ends_at'], 'subscriptions_user_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_age_lang_idx');
            $table->dropIndex('activities_tier_free_lang_idx');
            $table->dropIndex('activities_skill_lang_idx');
        });
        Schema::table('child_activity_progress', function (Blueprint $table) {
            $table->dropIndex('cap_child_completed_idx');
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_user_active_idx');
        });
    }
};
