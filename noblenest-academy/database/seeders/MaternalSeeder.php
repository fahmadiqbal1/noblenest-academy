<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestrator for all maternal wellness module seed data.
 */
class MaternalSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MaternalContentSeeder::class,
            MaternalExercisePlanSeeder::class,
            MaternalMealPlanSeeder::class,
            MaternalEmergencySignSeeder::class,
            ContraindicationMatrixSeeder::class,
        ]);
    }
}
