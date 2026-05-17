<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7 — Institutional licensing.
 *
 * Admin issues a signed invite to a school_admin via email; the invite token
 * is single-use and expires (default 14 days).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_admin_invites', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('school_name');
            $table->unsignedInteger('seats')->default(1);
            $table->string('invite_token', 64)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_admin_invites');
    }
};
