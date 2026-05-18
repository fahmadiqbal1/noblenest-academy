<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Q1: quiz_attempts.user_id was NOT NULL with an FK cascade, but the
 * /quizzes/{quiz}/submit route is public (no auth) and
 * QuizController::submit() inserts user_id = null for guests → every
 * guest quiz submission 500'd on a NOT NULL violation.
 *
 * Make it nullable and switch the FK to nullOnDelete so anonymous
 * attempts are allowed and a GDPR user-erase anonymises (rather than
 * cascade-deletes) their historical attempts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
