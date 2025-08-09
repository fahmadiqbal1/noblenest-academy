<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class BasicCourseSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::count() > 0) {
            return;
        }

        $samples = [
            [
                'title' => 'Foundations of Early Childhood Psychology',
                'slug' => 'parents-foundations',
                'description' => 'Parent Academy: attachment, bonding, routines, and positive discipline.',
            ],
            [
                'title' => 'Month 24 Unit — Early Years',
                'slug' => 'early-years-m24',
                'description' => 'Tracing numbers 1–5, ABAB patterns, mealtime manners, and outdoor play.',
            ],
            [
                'title' => 'Robotics Simulation L1',
                'slug' => 'robotics-sim-l1',
                'description' => 'Introduction to virtual sensors, actuators, and basic logic for kids 7–10.',
            ],
        ];

        foreach ($samples as $s) {
            Course::firstOrCreate(['slug' => $s['slug']], $s);
        }
    }
}
