<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('parent_id');
            }
            if (!Schema::hasColumn('users', 'preferred_language')) {
                $table->string('preferred_language', 10)->nullable()->after('age');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'preferred_language')) {
                $table->dropColumn('preferred_language');
            }
            if (Schema::hasColumn('users', 'age')) {
                $table->dropColumn('age');
            }
        });
    }
};
