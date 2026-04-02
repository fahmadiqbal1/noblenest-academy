<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add age_tier to child_profiles for fast lookup
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->string('age_tier', 20)->nullable()->after('date_of_birth')
                ->comment('baby (0-23m), toddler (24-47m), preschool (48-71m), school (72-120m)');
            $table->unsignedInteger('streak_days')->default(0)->after('age_tier');
            $table->date('last_activity_date')->nullable()->after('streak_days');
            $table->string('share_card_url')->nullable()->after('last_activity_date');
        });
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->dropColumn(['age_tier', 'streak_days', 'last_activity_date', 'share_card_url']);
        });
    }
};
