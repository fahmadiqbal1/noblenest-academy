<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->nullable()->unique();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('age_months_min')->default(0);
            $table->unsignedTinyInteger('age_months_max')->default(120);
            $table->enum('domain', ['cognitive', 'motor', 'language', 'social', 'creative', 'literacy', 'numeracy'])->default('cognitive');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('child_milestone_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'achieved'])->default('not_started');
            $table->timestamp('achieved_at')->nullable();
            $table->text('parent_note')->nullable();
            $table->timestamps();

            $table->unique(['child_profile_id', 'milestone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_milestone_progress');
        Schema::dropIfExists('milestones');
    }
};
