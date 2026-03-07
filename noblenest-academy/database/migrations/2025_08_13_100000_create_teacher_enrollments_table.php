<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_course_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending|active|completed|cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid|paid|refunded
            $table->string('payment_provider')->nullable();
            $table->string('payment_ref')->nullable();
            $table->decimal('amount_paid', 8, 2)->nullable();
            $table->string('currency', 8)->default('USD');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'teacher_course_id']);
            $table->index('teacher_course_id');
            $table->index('status');
        });

        // Invite links that teachers generate to share with students
        Schema::create('invite_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_course_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('label')->nullable();            // optional human name
            $table->unsignedInteger('max_uses')->nullable(); // null = unlimited
            $table->unsignedInteger('uses')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invite_links');
        Schema::dropIfExists('teacher_enrollments');
    }
};
