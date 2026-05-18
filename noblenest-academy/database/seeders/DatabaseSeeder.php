<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Default login credentials (all passwords: Password1!):
     *   Admin  — admin@noblenest.test
     *   Parent — parent@noblenest.test
     */
    public function run(): void
    {
        // ── Development credentials gate ─────────────────────────────────────
        // The test accounts below use a known password and must NEVER be seeded
        // in production. Content seeders (activities, curriculum, pricing) run
        // unconditionally because they are required for the app to function.
        if (! app()->environment('local', 'testing')) {
            $this->command->info('Production environment — skipping development credential seeding.');
            $this->command->info('Calling content seeders only...');
            $this->call([
                BasicCourseSeeder::class,
                CurriculumSeeder::class,
                PricingTierSeeder::class,
                BadgeSeeder::class,
                MilestoneSeeder::class,
                ActivitySeeder::class,
                ActivityStepSeeder::class,
                BabyActivitySeeder::class,
                ToddlerActivitySeeder::class,
                EmotionalRegulationActivitySeeder::class,
                PreschoolActivitySeeder::class,
                SchoolActivitySeeder::class,
                ExecutiveFunctionSeeder::class,
                EmotionalRegulationSeeder::class,
                ThematicJourneySeeder::class,
            ]);

            return;
        }

        $defaultPassword = Hash::make('Password1!');

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@noblenest.test',
                'role' => 'Admin',
                'email_verified_at' => now(),
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Parent User',
                'email' => 'parent@noblenest.test',
                'role' => 'Parent',
                'email_verified_at' => now(),
                'password' => $defaultPassword,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->call([
            // ── Core platform data ──────────────────────────────────────────
            BasicCourseSeeder::class,
            CurriculumSeeder::class,
            PricingTierSeeder::class,
            BadgeSeeder::class,
            MilestoneSeeder::class,

            // ── Age-tier activity libraries (must run before thematic maps) ─
            ActivitySeeder::class,
            ActivityStepSeeder::class,
            BabyActivitySeeder::class,         // 0–12 months
            ToddlerActivitySeeder::class,       // 12–36 months
            EmotionalRegulationActivitySeeder::class, // toddler ER activities (breathing, feelings)
            PreschoolActivitySeeder::class,     // 3–6 years
            SchoolActivitySeeder::class,        // 6–12 years

            // ── Specialist curriculum tracks (new — Phase 4) ────────────────
            ExecutiveFunctionSeeder::class,     // Soroban / Shichida / Kumon EF
            EmotionalRegulationSeeder::class,   // Zones of Regulation / Polyvagal / Islamic ER

            // ── Cross-curricular thematic journeys (depends on all above) ───
            ThematicJourneySeeder::class,       // 16 journeys × 4 weeks × 10 activity slots

            // ── Demo data (LOCAL/TESTING ONLY — Password1! accounts) ────────
            // Without these the demo parent has zero children and the
            // child dashboard / activity-player journey cannot be exercised.
            DemoChildrenSeeder::class,          // demo families + child profiles
            DemoOrchestratorSeeder::class,      // demo AI jobs for the Admin
        ]);
    }
}
