<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add room_url and recording to class_sessions
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('room_url')->nullable()->after('status');
            $table->string('daily_room_name')->nullable()->after('room_url');
            $table->string('recording_url')->nullable()->after('daily_room_name');
            $table->unsignedSmallInteger('max_participants')->default(20)->after('recording_url');
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn(['room_url', 'daily_room_name', 'recording_url', 'max_participants']);
        });
    }
};
