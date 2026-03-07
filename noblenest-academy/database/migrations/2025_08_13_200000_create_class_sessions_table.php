<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Scheduled live class sessions
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('status')->default('scheduled'); // scheduled|live|ended|cancelled
            $table->string('room_id', 64)->unique();        // stable room identifier
            $table->string('meeting_url')->nullable();      // optional external meeting URL
            $table->timestamps();

            $table->index('teacher_course_id');
            $table->index('starts_at');
            $table->index('status');
        });

        // Per-user tokens granting access to a classroom session
        Schema::create('session_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('role')->default('student'); // student|teacher
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['class_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_tokens');
        Schema::dropIfExists('class_sessions');
    }
};
