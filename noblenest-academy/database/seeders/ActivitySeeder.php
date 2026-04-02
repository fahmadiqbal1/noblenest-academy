<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            // ============================================================
            // BABY TIER — 0–23 months (is_free = true, first 10 are free)
            // ============================================================
            ['title' => 'Peek-a-Boo',              'emoji' => '👀', 'age_tier' => 'baby',      'subject' => 'social',   'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'A classic game that builds object permanence and social bonding.'],
            ['title' => 'Mirror Play',              'emoji' => '🪞', 'age_tier' => 'baby',      'subject' => 'social',   'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Let baby explore their reflection to develop self-awareness.'],
            ['title' => 'Singing Nursery Rhymes',   'emoji' => '🎵', 'age_tier' => 'baby',      'subject' => 'language', 'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Develop early phonics and rhythm through song.'],
            ['title' => 'Tummy Time',               'emoji' => '🐛', 'age_tier' => 'baby',      'subject' => 'motor',    'age_min' => 0, 'age_max' => 1, 'is_free' => true,  'description' => 'Strengthens neck and upper body muscles.'],
            ['title' => 'Sensory Touch Exploration','emoji' => '🖐️', 'age_tier' => 'baby',      'subject' => 'sensory',  'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Introduce textures: soft, rough, smooth, bumpy.'],
            ['title' => 'Clap and Tap',             'emoji' => '👏', 'age_tier' => 'baby',      'subject' => 'motor',    'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Develop hand coordination through rhythmic clapping.'],
            ['title' => 'Animal Sounds',            'emoji' => '🐮', 'age_tier' => 'baby',      'subject' => 'language', 'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Learn animal sounds to build early vocabulary.'],
            ['title' => 'Water Play',               'emoji' => '💧', 'age_tier' => 'baby',      'subject' => 'sensory',  'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Explore water sensations safely (supervised).'],
            ['title' => 'Stack and Knock',          'emoji' => '🧱', 'age_tier' => 'baby',      'subject' => 'cognitive','age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'Stack soft blocks and knock them down — cause and effect!'],
            ['title' => 'First Words Flash Cards',  'emoji' => '🃏', 'age_tier' => 'baby',      'subject' => 'language', 'age_min' => 0, 'age_max' => 2, 'is_free' => true,  'description' => 'High-contrast images and simple words for early visual literacy.'],

            // ============================================================
            // PRESCHOOL TIER — 4–5 years
            // ============================================================
            ['title' => 'Counting with Bears',      'emoji' => '🐻', 'age_tier' => 'preschool', 'subject' => 'math',     'age_min' => 3, 'age_max' => 5, 'is_free' => true,  'description' => 'Count colourful bear counters from 1 to 10.'],
            ['title' => 'Alphabet Tracing',         'emoji' => '✏️', 'age_tier' => 'preschool', 'subject' => 'literacy', 'age_min' => 3, 'age_max' => 5, 'is_free' => true,  'description' => 'Trace letters A–Z and identify starting sounds.'],
            ['title' => 'Shape Hunt',               'emoji' => '🔷', 'age_tier' => 'preschool', 'subject' => 'math',     'age_min' => 3, 'age_max' => 5, 'is_free' => true,  'description' => 'Find circles, squares, and triangles around the house.'],
            ['title' => 'Colouring the Rainforest', 'emoji' => '🌳', 'age_tier' => 'preschool', 'subject' => 'art',      'age_min' => 3, 'age_max' => 5, 'is_free' => true,  'description' => 'Colour a rainforest scene while learning about plants.'],
            ['title' => 'Simple Patterns',          'emoji' => '🔴', 'age_tier' => 'preschool', 'subject' => 'math',     'age_min' => 3, 'age_max' => 5, 'is_free' => true,  'description' => 'Complete red-blue-red-blue patterns using household items.'],
            ['title' => 'Storytime: The Very Hungry Caterpillar', 'emoji' => '📗', 'age_tier' => 'preschool', 'subject' => 'literacy', 'age_min' => 2, 'age_max' => 5, 'is_free' => true, 'description' => 'Read along and count the foods the caterpillar eats.'],
            ['title' => 'Feelings Check-In',        'emoji' => '😊', 'age_tier' => 'preschool', 'subject' => 'social',   'age_min' => 3, 'age_max' => 6, 'is_free' => true,  'description' => 'Identify and name 5 basic emotions using illustrated faces.'],
            ['title' => 'Weather Journal',          'emoji' => '☀️', 'age_tier' => 'preschool', 'subject' => 'science',  'age_min' => 3, 'age_max' => 6, 'is_free' => true,  'description' => 'Observe and draw the weather each morning.'],
            ['title' => 'Musical Freeze',           'emoji' => '🎶', 'age_tier' => 'preschool', 'subject' => 'motor',    'age_min' => 3, 'age_max' => 6, 'is_free' => true,  'description' => 'Dance and freeze — builds listening skills and body control.'],
            ['title' => 'Seeds and Plants',         'emoji' => '🌱', 'age_tier' => 'preschool', 'subject' => 'science',  'age_min' => 3, 'age_max' => 6, 'is_free' => true,  'description' => 'Plant a bean seed and observe its growth over days.'],

            // ============================================================
            // SCHOOL / STEM TIER — 6–12 years
            // ============================================================
            ['title' => 'Introduction to Coding: Loops',     'emoji' => '💻', 'age_tier' => 'school', 'subject' => 'technology', 'age_min' => 6, 'age_max' => 12, 'is_free' => true, 'description' => 'Learn what a loop is by giving instructions to a robot.'],
            ['title' => 'The Water Cycle Experiment',         'emoji' => '🌧️', 'age_tier' => 'school', 'subject' => 'science',    'age_min' => 6, 'age_max' => 12, 'is_free' => true, 'description' => 'Create a mini water cycle in a ziplock bag.'],
            ['title' => 'Fractions with Pizza',               'emoji' => '🍕', 'age_tier' => 'school', 'subject' => 'math',       'age_min' => 6, 'age_max' => 12, 'is_free' => true, 'description' => 'Understand halves, thirds, and quarters using a paper pizza.'],
            ['title' => 'Build a Bridge (Engineering)',       'emoji' => '🌉', 'age_tier' => 'school', 'subject' => 'engineering','age_min' => 7, 'age_max' => 12, 'is_free' => true, 'description' => 'Using paper and tape, build the strongest bridge you can.'],
            ['title' => 'Ode to the Ocean (Poetry)',         'emoji' => '🌊', 'age_tier' => 'school', 'subject' => 'language',   'age_min' => 7, 'age_max' => 12, 'is_free' => true, 'description' => 'Write a short poem about the ocean using descriptive language.'],
            ['title' => 'Multiplication with Arrays',        'emoji' => '✖️', 'age_tier' => 'school', 'subject' => 'math',       'age_min' => 7, 'age_max' => 12, 'is_free' => true, 'description' => 'Visualise multiplication using rows and columns of objects.'],
            ['title' => 'Map Reading: Continents & Oceans',  'emoji' => '🗺️', 'age_tier' => 'school', 'subject' => 'geography',  'age_min' => 7, 'age_max' => 12, 'is_free' => true, 'description' => 'Label a blank world map with the 7 continents and 5 oceans.'],
            ['title' => 'Sound & Music Science',             'emoji' => '🎸', 'age_tier' => 'school', 'subject' => 'science',    'age_min' => 7, 'age_max' => 12, 'is_free' => true, 'description' => 'Explore how changing string tension changes pitch.'],
            ['title' => 'Story Structure: Beginning-Middle-End', 'emoji' => '📖', 'age_tier' => 'school', 'subject' => 'literacy', 'age_min' => 6, 'age_max' => 12, 'is_free' => true, 'description' => 'Write a 3-paragraph story with a clear structure.'],
            ['title' => 'Introduction to Algebra (Balancing)', 'emoji' => '⚖️', 'age_tier' => 'school', 'subject' => 'math',     'age_min' => 9, 'age_max' => 12, 'is_free' => true, 'description' => 'Solve simple x equations by balancing both sides of a scale.'],
        ];

        foreach ($activities as $activity) {
            Activity::updateOrCreate(
                ['title' => $activity['title']],
                array_merge($activity, [
                    'language'   => 'en',
                    'like_count' => 0,
                ])
            );
        }
    }
}
