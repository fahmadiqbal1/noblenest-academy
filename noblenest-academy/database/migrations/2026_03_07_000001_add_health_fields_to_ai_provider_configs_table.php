<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_provider_configs', function (Blueprint $table) {
            $table->string('connection_status')->default('unchecked')->after('is_active');
            $table->text('connection_message')->nullable()->after('connection_status');
            $table->timestamp('last_checked_at')->nullable()->after('connection_message');
            $table->timestamp('last_live_at')->nullable()->after('last_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('ai_provider_configs', function (Blueprint $table) {
            $table->dropColumn([
                'connection_status',
                'connection_message',
                'last_checked_at',
                'last_live_at',
            ]);
        });
    }
};