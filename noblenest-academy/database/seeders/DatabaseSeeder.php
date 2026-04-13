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
     *   Admin   — admin@noblenest.test
     *   Teacher — teacher@noblenest.test
     *   Parent  — parent@noblenest.test
     *   Student — student@noblenest.test
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password1!');

        $users = [
            [
                'name'               => 'Admin User',
                'email'              => 'admin@noblenest.test',
                'role'               => 'Admin',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
            [
                'name'               => 'Teacher User',
                'email'              => 'teacher@noblenest.test',
                'role'               => 'Teacher',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
            [
                'name'               => 'Parent User',
                'email'              => 'parent@noblenest.test',
                'role'               => 'Parent',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
            [
                'name'               => 'Student User',
                'email'              => 'student@noblenest.test',
                'role'               => 'Student',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
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

            // ── Maternal & prenatal track ───────────────────────────────────
            MaternalSeeder::class,   // orchestrates all maternal sub-seeders internally

            // ── Age-tier activity libraries (must run before thematic maps) ─
            ActivitySeeder::class,
            ActivityStepSeeder::class,
            BabyActivitySeeder::class,         // 0–12 months
            ToddlerActivitySeeder::class,       // 12–36 months (new — Phase 4)
            PreschoolActivitySeeder::class,     // 3–6 years
            SchoolActivitySeeder::class,        // 6–12 years

            // ── Specialist curriculum tracks (new — Phase 4) ────────────────
            ExecutiveFunctionSeeder::class,     // Soroban / Shichida / Kumon EF
            EmotionalRegulationSeeder::class,   // Zones of Regulation / Polyvagal / Islamic ER

            // ── Cross-curricular thematic journeys (depends on all above) ───
            ThematicJourneySeeder::class,       // 16 journeys × 4 weeks × 10 activity slots
        ]);
    }
}
