<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_minutes')->nullable()->after('age_max');
            $table->string('difficulty', 20)->nullable()->after('duration_minutes'); // easy|medium|hard
            $table->string('thumbnail_url')->nullable()->after('difficulty');
            $table->text('instructions')->nullable()->after('description');
            $table->text('materials_needed')->nullable()->after('instructions');
            $table->text('learning_objectives')->nullable()->after('materials_needed');
            $table->string('age_group', 20)->nullable()->after('learning_objectives'); // baby|preschool|school
            $table->boolean('is_muslim_only')->default(false)->after('is_free');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'duration_minutes',
                'difficulty',
                'thumbnail_url',
                'instructions',
                'materials_needed',
                'learning_objectives',
                'age_group',
                'is_muslim_only',
            ]);
        });
    }
};
