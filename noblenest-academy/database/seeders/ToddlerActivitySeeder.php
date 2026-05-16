<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ToddlerActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            ['title' => 'Color Sorting Game', 'subject' => 'math', 'cognitive_domain' => 'math'],
            ['title' => 'Animal Sound Fun', 'subject' => 'language', 'cognitive_domain' => 'language'],
            ['title' => 'Dinosaur Stomp Dance', 'subject' => 'physical', 'cognitive_domain' => 'physical_development'],
            ['title' => 'Playdough Creation', 'subject' => 'art', 'cognitive_domain' => 'art'],
            ['title' => 'Block Stacking Challenge', 'subject' => 'math', 'cognitive_domain' => 'math'],
        ];

        foreach ($activities as $data) {
            Activity::firstOrCreate(['title' => $data['title']], [
                'description' => "Toddler activity about {$data['subject']}",
                'instructions' => 'Step-by-step guide.',
                'materials_needed' => null,
                'duration_minutes' => 15,
                'difficulty' => 'easy',
                'age_tier' => 'toddler',
                'subject' => $data['subject'],
                'language' => 'english',
                'is_free' => true,
                'mess_level' => 'low',
                'safety_warnings' => null,
                'adaptations' => json_encode(['easier' => 'Simplify', 'harder' => 'Complicate']),
                'cognitive_domain' => $data['cognitive_domain'],
                'developmental_domains' => json_encode(['cognitive']),
                'materials_cost' => 'free',
                'parent_involvement' => 'guided',
                'instructions_for_parent' => 'Guide your toddler.',
            ]);
        }
    }
}
