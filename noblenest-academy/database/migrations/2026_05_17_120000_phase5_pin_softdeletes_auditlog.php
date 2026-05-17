<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 5 — Parent PIN + GDPR soft-deletes + Audit Log.
 *
 * - users.parent_pin_hash : 4-digit PIN hashed via bcrypt for parent-mode
 *   re-entry gating (used by `parent.pin` middleware on sensitive routes).
 * - SoftDeletes (deleted_at) added to users, child_profiles,
 *   child_activity_progress so PrivacyController::deleteData() can run a
 *   reversible erase and HardDeleteParentDataJob can finalise after 30 days.
 * - audit_log_entries : immutable trail of privacy/admin actions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'parent_pin_hash')) {
                $table->string('parent_pin_hash')->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('child_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('child_profiles', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (Schema::hasTable('child_activity_progress')) {
            Schema::table('child_activity_progress', function (Blueprint $table) {
                if (! Schema::hasColumn('child_activity_progress', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        Schema::create('audit_log_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable()->index();
            $table->string('action', 64);
            $table->string('target_type', 64)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at'], 'ale_action_created_idx');
            $table->index(['target_type', 'target_id'], 'ale_target_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log_entries');

        if (Schema::hasTable('child_activity_progress')) {
            Schema::table('child_activity_progress', function (Blueprint $table) {
                if (Schema::hasColumn('child_activity_progress', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }

        Schema::table('child_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('child_profiles', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'parent_pin_hash')) {
                $table->dropColumn('parent_pin_hash');
            }
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
