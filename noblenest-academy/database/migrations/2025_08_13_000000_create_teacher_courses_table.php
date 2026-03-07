<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teacher's own courses (completely separate from Noble Nest Academy curriculum)
        Schema::create('teacher_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('what_you_learn')->nullable();     // bullet list stored as JSON or text
            $table->string('subject')->nullable();          // e.g. Math, English, Science
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->string('level')->default('beginner');   // beginner|intermediate|advanced
            $table->string('language', 8)->default('en');
            $table->decimal('price', 8, 2)->default(0);    // 0 = free
            $table->string('currency', 8)->default('USD');
            $table->string('thumbnail')->nullable();        // stored path
            $table->string('syllabus_file')->nullable();    // uploaded PDF/doc
            $table->string('status')->default('draft');     // draft|published|archived
            $table->unsignedInteger('max_students')->nullable(); // null = unlimited
            $table->timestamps();

            $table->index('teacher_id');
            $table->index('status');
            $table->index('subject');
        });

        // Sections / chapters within a teacher course
        Schema::create('teacher_course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->index('teacher_course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_course_sections');
        Schema::dropIfExists('teacher_courses');
    }
};
