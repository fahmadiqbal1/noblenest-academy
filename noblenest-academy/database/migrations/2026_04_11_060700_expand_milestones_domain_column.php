<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand milestones.domain from enum to varchar(50).
     *
     * Phase 5 introduces new cognitive domains (executive_function,
     * emotional_regulation, mental_arithmetic, focus_attention) that
     * exceed the original enum definition.
     */
    public function up(): void
    {
        // MySQL enums cannot be easily altered via Schema builder. Use raw SQL on MySQL.
        // SQLite (used by tests) has no enums and no MODIFY COLUMN — go through
        // Laravel's Schema::change() which rebuilds the table portably.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('milestones', function (Blueprint $table) {
                $table->string('domain', 50)->default('cognitive')->change();
            });

            return;
        }
        DB::statement("ALTER TABLE milestones MODIFY COLUMN domain VARCHAR(50) DEFAULT 'cognitive'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // No enum reversal on SQLite — leave the column as varchar.
            return;
        }
        DB::statement("ALTER TABLE milestones MODIFY COLUMN domain ENUM('cognitive','motor','language','social','creative','literacy','numeracy') DEFAULT 'cognitive'");
    }
};
