<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maternal_contents', function (Blueprint $table) {
            $table->json('skills_improved')->nullable()->change();
            $table->text('health_benefit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maternal_contents', function (Blueprint $table) {
            $table->json('skills_improved')->nullable(false)->change();
            $table->text('health_benefit')->nullable(false)->change();
        });
    }
};
