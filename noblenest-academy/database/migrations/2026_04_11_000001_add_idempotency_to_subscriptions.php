<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Idempotency key to prevent duplicate webhook processing
            $table->string('idempotency_key', 255)->nullable()->unique()->after('provider_id')
                  ->comment('Stripe session ID or provider transaction ID — prevents replay attacks');

            // Compound index for fast deduplication lookups in webhook handler
            $table->index(['provider', 'provider_id'], 'subscriptions_provider_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropIndex('subscriptions_provider_lookup_idx');
            $table->dropColumn('idempotency_key');
        });
    }
};
