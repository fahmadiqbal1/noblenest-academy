<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_meal_plans', function (Blueprint $table) {
            $table->id();
            $table->string('stage', 30);
            $table->unsignedTinyInteger('week_number');
            $table->string('day_of_week', 10);
            $table->json('breakfast');
            $table->json('morning_snack')->nullable();
            $table->json('lunch');
            $table->json('afternoon_snack')->nullable();
            $table->json('dinner');
            $table->text('hydration_notes')->nullable();
            $table->string('herb_tea_recommendation')->nullable();
            $table->json('key_nutrients');
            $table->text('benefit_explanation');
            $table->string('language', 8)->default('en');
            $table->timestamps();

            $table->index(['stage', 'week_number', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_meal_plans');
    }
};
