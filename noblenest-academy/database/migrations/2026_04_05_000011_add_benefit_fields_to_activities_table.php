<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'benefit_explanation')) {
                $table->text('benefit_explanation')->nullable()->after('learning_objectives');
            }
            if (! Schema::hasColumn('activities', 'skills_improved')) {
                $table->json('skills_improved')->nullable()->after('benefit_explanation');
            }
            if (! Schema::hasColumn('activities', 'health_benefit')) {
                $table->text('health_benefit')->nullable()->after('skills_improved');
            }
            if (! Schema::hasColumn('activities', 'learning_modalities')) {
                $table->json('learning_modalities')->nullable()->after('health_benefit');
            }
            if (! Schema::hasColumn('activities', 'primary_modality')) {
                $table->string('primary_modality', 15)->nullable()->after('learning_modalities');
            }
            if (! Schema::hasColumn('activities', 'subtitle_url')) {
                $table->string('subtitle_url')->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('activities', 'interactive_type')) {
                $table->string('interactive_type', 20)->nullable()->after('subtitle_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'benefit_explanation',
                'skills_improved',
                'health_benefit',
                'learning_modalities',
                'primary_modality',
                'subtitle_url',
                'interactive_type',
            ]);
        });
    }
};
