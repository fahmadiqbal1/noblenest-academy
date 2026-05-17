<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Parental context fields
            $table->enum('mess_level', ['low', 'medium', 'high'])->default('low')->after('difficulty')
                ->comment('How messy is this activity? Helps parents prepare');

            $table->json('safety_warnings')->nullable()->after('mess_level')
                ->comment('Array of safety strings, e.g. ["Choking hazard: supervise closely"]');

            $table->json('adaptations')->nullable()->after('safety_warnings')
                ->comment('Object with "easier" and "harder" keys for differentiation');

            // Cognitive & developmental classification
            $table->string('cognitive_domain', 100)->nullable()->after('adaptations')
                ->comment('Primary cognitive domain: working_memory, attention, inhibitory_control, etc.');

            $table->json('developmental_domains')->nullable()->after('cognitive_domain')
                ->comment('Array of domains: fine_motor, gross_motor, language, numeracy, executive_function, etc.');

            // Logistical context for parents
            $table->enum('materials_cost', ['free', 'low', 'medium'])->default('free')->after('developmental_domains')
                ->comment('Estimated cost of materials needed');

            $table->enum('parent_involvement', ['independent', 'guided', 'collaborative'])->default('guided')->after('materials_cost')
                ->comment('How much parental involvement this activity needs');

            // Index the cognitive domain for curriculum health checks
            $table->index('cognitive_domain', 'activities_cognitive_domain_idx');
            $table->index('mess_level', 'activities_mess_level_idx');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_cognitive_domain_idx');
            $table->dropIndex('activities_mess_level_idx');
            $table->dropColumn([
                'mess_level', 'safety_warnings', 'adaptations',
                'cognitive_domain', 'developmental_domains',
                'materials_cost', 'parent_involvement',
            ]);
        });
    }
};
