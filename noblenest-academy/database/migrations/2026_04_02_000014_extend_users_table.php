<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend users table with geo-pricing, region, and activation fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('preferred_language');
            $table->string('pricing_tier_code', 20)->nullable()->after('country_code');
            $table->unsignedInteger('activities_completed')->default(0)->after('pricing_tier_code');
            $table->boolean('is_onboarded')->default(false)->after('activities_completed');
            $table->string('pricing_tier')->nullable()->after('is_onboarded');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'pricing_tier_code', 'activities_completed', 'is_onboarded', 'pricing_tier']);
        });
    }
};
