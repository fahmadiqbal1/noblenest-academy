<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedSmallInteger('duration')->nullable(); // minutes
            $table->string('language', 8)->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index('module_id');
        });

        Schema::create('activity_lesson', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->unique(['lesson_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_lesson');
        Schema::dropIfExists('lessons');
    }
};
