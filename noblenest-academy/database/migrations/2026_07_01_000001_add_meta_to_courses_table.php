<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('age_min')->nullable()->after('description');
            $table->unsignedTinyInteger('age_max')->nullable()->after('age_min');
            $table->string('color', 20)->nullable()->after('age_max');
            $table->string('emoji', 10)->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['age_min', 'age_max', 'color', 'emoji']);
        });
    }
};
