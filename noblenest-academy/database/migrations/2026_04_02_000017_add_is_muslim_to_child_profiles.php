<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            // null = not answered, true = Muslim, false = non-Muslim
            // Controls Quran + Islamic studies curriculum access
            $table->boolean('is_muslim')->nullable()->default(null)->after('gender')
                ->comment('null=not answered, true=Muslim household, false=non-Muslim. Gates Quran/Islamic curriculum.');
        });
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->dropColumn('is_muslim');
        });
    }
};
