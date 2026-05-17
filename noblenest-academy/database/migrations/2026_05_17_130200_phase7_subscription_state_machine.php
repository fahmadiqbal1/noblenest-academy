<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7 — Subscription state machine.
 *
 * Existing `status` column is an enum that doesn't include 'paused'.
 * We widen to string and add a paused_at timestamp so we can resume cleanly.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Widen the legacy enum to a plain string so we can store the Phase 7
        // 'paused' state that wasn't in the original enum. Driver-specific:
        //   MySQL / MariaDB → ALTER TABLE MODIFY (cheap).
        //   SQLite           → drop + re-add as string (SQLite's CHECK constraint
        //                      from the original ENUM rejects new values).
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            try {
                DB::statement("ALTER TABLE subscriptions MODIFY status VARCHAR(32) NOT NULL DEFAULT 'active'");
            } catch (Throwable $e) {
                // Older drivers may use slightly different syntax — ignore.
            }
        } elseif ($driver === 'sqlite') {
            // Use a temp column to preserve data through the swap.
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('status_tmp', 32)->nullable();
            });
            DB::statement('UPDATE subscriptions SET status_tmp = status');
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('status', 32)->default('active');
            });
            DB::statement("UPDATE subscriptions SET status = COALESCE(status_tmp, 'active')");
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('status_tmp');
            });
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'paused_at')) {
                $table->timestamp('paused_at')->nullable()->after('cancel_at');
            }
            if (! Schema::hasColumn('subscriptions', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('paused_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'paused_at')) {
                $table->dropColumn('paused_at');
            }
            if (Schema::hasColumn('subscriptions', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }
        });
    }
};
