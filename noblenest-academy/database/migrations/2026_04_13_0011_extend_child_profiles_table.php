<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->enum('mood_current', ['calm', 'happy', 'tired', 'frustrated', 'excited', 'unknown'])
                  ->default('unknown');
            $table->timestamp('mood_captured_at')->nullable();
            $table->decimal('eli_weight', 3, 2)->default(1.00);
        });
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->dropColumn(['mood_current', 'mood_captured_at', 'eli_weight']);
        });
    }
};
