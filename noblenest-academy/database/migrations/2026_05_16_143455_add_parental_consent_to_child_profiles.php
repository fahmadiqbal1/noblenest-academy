<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 5 — under-13 Parental Consent (COPPA + GDPR-K).
 *
 * Records the timestamp + parent's IP / user-agent when consent is recorded.
 * The ParentalConsentMiddleware reads `parental_consent_at` to decide whether
 * to gate the child's content; the IP + UA are kept for audit per COPPA
 * record-keeping rules.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->timestamp('parental_consent_at')->nullable()->after('updated_at');
            $table->string('parental_consent_ip', 45)->nullable()->after('parental_consent_at');
            $table->string('parental_consent_user_agent', 255)->nullable()->after('parental_consent_ip');
        });
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->dropColumn(['parental_consent_at', 'parental_consent_ip', 'parental_consent_user_agent']);
        });
    }
};
