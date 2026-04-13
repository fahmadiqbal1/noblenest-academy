<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class SchoolAgeActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            ['title' => 'Math Multiplication Games', 'subject' => 'math', 'cognitive_domain' => 'math'],
            ['title' => 'Reading Comprehension Story', 'subject' => 'language', 'cognitive_domain' => 'language'],
            ['title' => 'Science Experiment: Volcanoes', 'subject' => 'science', 'cognitive_domain' => 'science'],
            ['title' => 'Creating Digital Art', 'subject' => 'art', 'cognitive_domain' => 'art'],
            ['title' => 'Coding Basics with Blocks', 'subject' => 'stem', 'cognitive_domain' => 'executive_function'],
        ];

        foreach ($activities as $data) {
            Activity::firstOrCreate(['title' => $data['title']], [
                'description' => "School-age activity about {$data['subject']}",
                'instructions' => 'Detailed instructions for school children.',
                'materials' => [],
                'duration_minutes' => 30,
                'difficulty' => 'medium',
                'age_tier' => 'school',
                'subject' => $data['subject'],
                'language' => 'english',
                'is_free' => true,
                'mess_level' => 'low',
                'safety_warnings' => [],
                'adaptations' => ['easier' => 'Scaffold', 'harder' => 'Challenge'],
                'cognitive_domain' => $data['cognitive_domain'],
                'developmental_domains' => ['cognitive', 'attention'],
                'materials_cost' => 0,
                'parent_involvement' => 'moderate',
                'instructions_for_parent' => 'Support your school-age child.',
            ]);
        }
    }
}
