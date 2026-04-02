<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('name', 150);
            $table->string('emoji', 10)->nullable();
            $table->text('description')->nullable();
            $table->string('icon_url')->nullable();
            $table->enum('badge_type', ['milestone', 'streak', 'course', 'social', 'special', 'subject'])->default('milestone');
            $table->json('criteria')->nullable();
            $table->unsignedInteger('required_value')->default(1); // e.g., complete N activities
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('child_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('awarded_at');
            $table->timestamps();

            $table->unique(['child_profile_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_badges');
        Schema::dropIfExists('badges');
    }
};
