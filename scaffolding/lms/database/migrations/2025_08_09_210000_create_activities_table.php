<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->string('skill')->nullable();
            $table->unsignedTinyInteger('duration')->nullable(); // in minutes
            $table->string('language', 8)->nullable();
            $table->string('activity_type')->nullable();
            $table->string('media_url')->nullable();
            $table->boolean('is_rtl')->default(false);
            $table->timestamps();
            $table->index(['age_min', 'age_max']);
            $table->index('skill');
            $table->index('language');
            $table->index('activity_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
