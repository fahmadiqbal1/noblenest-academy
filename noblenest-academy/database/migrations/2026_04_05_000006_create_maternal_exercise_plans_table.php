<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_exercise_plans', function (Blueprint $table) {
            $table->id();
            $table->string('stage', 30);
            $table->unsignedTinyInteger('week_number');
            $table->string('day_of_week', 10);
            $table->string('routine_name');
            $table->json('exercises');
            $table->unsignedSmallInteger('total_duration_minutes')->default(20);
            $table->string('intensity', 15)->default('gentle');
            $table->text('warmup_instructions')->nullable();
            $table->text('cooldown_instructions')->nullable();
            $table->text('benefit_explanation');
            $table->string('cultural_origin', 20)->nullable();
            $table->text('safety_notes')->nullable();
            $table->json('contraindications')->nullable();
            $table->string('language', 8)->default('en');
            $table->timestamps();

            $table->index(['stage', 'week_number', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_exercise_plans');
    }
};
