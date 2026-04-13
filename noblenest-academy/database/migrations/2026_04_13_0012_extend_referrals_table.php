<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->enum('tier', ['tier1', 'tier2', 'tier3'])->nullable();
            $table->enum('attribution_source', ['direct', 'share_card', 'email', 'other'])->default('direct');
            $table->foreignId('share_card_share_id')
                  ->nullable()
                  ->constrained('share_card_shares')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['share_card_share_id']);
            $table->dropColumn(['tier', 'attribution_source', 'share_card_share_id']);
        });
    }
};
