<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7 — Plans + PPP (purchasing-power parity) extensions.
 *
 * Adds the spec-defined plan key, ISO-2 country override, billing interval,
 * and a JSON feature list to the existing region-based pricing_tiers table.
 *
 * region_code remains the legacy unique key; for Phase 7 spec plans we relax
 * the unique constraint by allowing the same region_code with a different
 * `key`+`country_code` row. PPP variants live as rows with the same `key`
 * but a non-null `country_code` (e.g., key=family country_code=IN).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_tiers', function (Blueprint $table) {
            // Spec plan key: freemium | individual | family | annual | institutional.
            $table->string('key', 40)->nullable()->after('id');
            // ISO-2 country override (null = global base price for this plan).
            $table->string('country_code', 2)->nullable()->after('key');
            // monthly | yearly | per_seat (institutional).
            $table->string('interval', 20)->nullable()->after('price_yearly');
            // Spec feature list — shown on /pricing.
            $table->json('features')->nullable()->after('interval');

            $table->index(['key', 'country_code'], 'pricing_tiers_key_country_idx');
        });

        // Region_code was unique; relax so we can have multiple PPP rows that
        // reuse the same region code. We use (key, country_code) as the new
        // logical unique identifier going forward.
        Schema::table('pricing_tiers', function (Blueprint $table) {
            try {
                $table->dropUnique('pricing_tiers_region_code_unique');
            } catch (Throwable $e) {
                // SQLite or older driver may not have the named index — ignore.
            }
        });
    }

    public function down(): void
    {
        Schema::table('pricing_tiers', function (Blueprint $table) {
            $table->dropIndex('pricing_tiers_key_country_idx');
            $table->dropColumn(['key', 'country_code', 'interval', 'features']);
        });
    }
};
