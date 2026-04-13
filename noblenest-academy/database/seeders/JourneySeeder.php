<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ThematicJourney;
use App\Models\WeeklyTheme;
use Illuminate\Database\Seeder;

class JourneySeeder extends Seeder
{
    /**
     * Seed 10 launch journeys across age tiers with thematic coherence.
     */
    public function run(): void
    {
        $journeys = [
            [
                'title' => 'The Ocean',
                'description' => 'Explore the wonders of the ocean through science, art, and adventure.',
                'age_tier' => 'preschool',
                'total_weeks' => 4,
                'emoji' => '🌊',
                'cover_color' => '#0066cc',
                'weeks' => [
                    ['week_number' => 1, 'theme_name' => 'Ocean Friends', 'big_idea' => 'Many creatures live in the ocean'],
                    ['week_number' => 2, 'theme_name' => 'Waves & Water', 'big_idea' => 'Water moves and changes'],
                    ['week_number' => 3, 'theme_name' => 'Coral Reefs', 'big_idea' => 'Ecosystems depend on each other'],
                    ['week_number' => 4, 'theme_name' => 'Ocean Discovery', 'big_idea' => 'We can learn and explore'],
                ],
            ],
            [
                'title' => 'Space & Stars',
                'description' => 'Journey through the cosmos with science, imagination, and wonder.',
                'age_tier' => 'school',
                'total_weeks' => 4,
                'emoji' => '🌟',
                'cover_color' => '#330066',
                'weeks' => [
                    ['week_number' => 1, 'theme_name' => 'The Moon', 'big_idea' => 'The moon orbits Earth'],
                    ['week_number' => 2, 'theme_name' => 'Planets', 'big_idea' => 'Our solar system is vast'],
                    ['week_number' => 3, 'theme_name' => 'Stars & Galaxies', 'big_idea' => 'The universe is huge'],
                    ['week_number' => 4, 'theme_name' => 'Space Exploration', 'big_idea' => 'Humans explore space'],
                ],
            ],
            [
                'title' => 'My Body, My Senses',
                'description' => 'Discover how your body works and experience the world through your senses.',
                'age_tier' => 'toddler',
                'total_weeks' => 4,
                'emoji' => '👅',
                'cover_color' => '#ff6699',
                'weeks' => [
                    ['week_number' => 1, 'theme_name' => 'What I Can See', 'big_idea' => 'Eyes help us see'],
                    ['week_number' => 2, 'theme_name' => 'What I Can Hear', 'big_idea' => 'Ears help us hear'],
                    ['week_number' => 3, 'theme_name' => 'What I Can Touch', 'big_idea' => 'Skin helps us feel'],
                    ['week_number' => 4, 'theme_name' => 'What I Can Taste', 'big_idea' => 'Tongues help us taste'],
                ],
            ],
            [
                'title' => 'Big Feelings, Big Heart',
                'description' => 'Learn to recognize, understand, and manage emotions with empathy and joy.',
                'age_tier' => 'preschool',
                'total_weeks' => 4,
                'emoji' => '❤️',
                'cover_color' => '#ff0000',
                'weeks' => [
                    ['week_number' => 1, 'theme_name' => 'Happy & Sad', 'big_idea' => 'All feelings are okay'],
                    ['week_number' => 2, 'theme_name' => 'Angry & Calm', 'big_idea' => 'We can calm ourselves'],
                    ['week_number' => 3, 'theme_name' => 'Scared & Brave', 'big_idea' => 'Bravery is trying anyway'],
                    ['week_number' => 4, 'theme_name' => 'Kindness & Love', 'big_idea' => 'We can help others'],
                ],
            ],
        ];

        foreach ($journeys as $journeyData) {
            $weeks = $journeyData['weeks'];
            unset($journeyData['weeks']);

            $journey = ThematicJourney::firstOrCreate(
                ['title' => $journeyData['title']],
                $journeyData
            );

            // Create weekly themes
            foreach ($weeks as $weekData) {
                WeeklyTheme::firstOrCreate(
                    [
                        'journey_id'   => $journey->id,
                        'week_number'  => $weekData['week_number'],
                    ],
                    [
                        'theme_name'       => $weekData['theme_name'],
                        'theme_description' => null,
                        'big_idea'         => $weekData['big_idea'],
                    ]
                );
            }
        }

        $this->command->info('Journeys seeded successfully!');
    }
}
