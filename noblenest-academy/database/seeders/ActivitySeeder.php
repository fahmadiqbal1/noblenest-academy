<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestrator � delegates to tier-specific seeders.
 * Each tier seeder owns its own activities array and runs independently.
 */
class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BabyActivitySeeder::class,
            PreschoolActivitySeeder::class,
            SchoolActivitySeeder::class,
        ]);
    }
}
