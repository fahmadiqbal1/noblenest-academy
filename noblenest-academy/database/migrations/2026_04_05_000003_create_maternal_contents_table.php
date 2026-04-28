<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            $table->string('content_type', 30);
            $table->string('stage', 30);
            $table->string('category', 30);
            $table->string('cultural_origin', 20)->nullable();

            $table->text('benefit_explanation');
            $table->json('skills_improved');
            $table->text('health_benefit');
            $table->text('safety_notes')->nullable();
            $table->json('contraindications')->nullable();

            $table->string('difficulty', 15)->default('gentle');
            $table->unsignedSmallInteger('duration_minutes')->default(10);
            $table->json('ingredients_or_materials')->nullable();
            $table->text('instructions')->nullable();

            $table->string('thumbnail_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('subtitle_url')->nullable();

            $table->boolean('is_free')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->string('moderation_status', 20)->default('pending');
            $table->string('medical_reviewer_name')->nullable();
            $table->string('reviewed_by_credential')->nullable();

            $table->string('language', 8)->default('en');
            $table->boolean('is_rtl')->default(false);
            $table->string('emoji', 10)->nullable();

            $table->timestamps();

            $table->index(['stage', 'content_type', 'language']);
            $table->index(['category', 'language']);
            $table->index(['cultural_origin']);
            $table->index(['moderation_status', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_contents');
    }
};
