<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ThematicJourney;
use App\Models\ThemeActivity;
use App\Models\WeeklyTheme;
use Illuminate\Database\Seeder;

class ThematicJourneySeeder extends Seeder
{
    /**
     * 16 thematic journeys across all age tiers.
     * Each journey contains 4 weeks; each week maps 10 activities across Mon-Fri (morning + afternoon).
     */
    public function run(): void
    {
        $this->command->info('🌍 Seeding thematic journeys...');

        foreach ($this->journeyData() as $journeyDef) {
            $journey = ThematicJourney::create([
                'title' => $journeyDef['title'],
                'description' => $journeyDef['description'],
                'age_tier' => $journeyDef['age_tier'],
                'emoji' => $journeyDef['emoji'],
                'cover_color' => $journeyDef['cover_color'],
                'total_weeks' => count($journeyDef['weeks']),
                'is_published' => true,
                'sort_order' => $journeyDef['sort_order'],
            ]);

            foreach ($journeyDef['weeks'] as $weekNum => $weekDef) {
                $week = WeeklyTheme::create([
                    'journey_id' => $journey->id,
                    'week_number' => $weekNum + 1,
                    'theme_name' => $weekDef['theme'],
                    'theme_description' => $weekDef['description'],
                    'theme_emoji' => $weekDef['emoji'],
                    'big_idea' => $weekDef['big_idea'],
                ]);

                $this->seedWeekActivities($week, $weekDef['activities'], $journey->age_tier);
            }

            $this->command->line("  ✓ {$journey->title} ({$journey->age_tier})");
        }

        $this->command->info('✅ Thematic journeys seeded.');
    }

    private function seedWeekActivities(WeeklyTheme $week, array $slots, string $ageTier): void
    {
        [$agMin, $agMax] = match ($ageTier) {
            'baby' => [0, 12],
            'toddler' => [12, 36],
            'preschool' => [36, 72],
            'school' => [72, 120],
            default => [0, 120],
        };

        foreach ($slots as $slot) {
            // Find a matching activity or skip
            $activity = Activity::where('subject', $slot['subject'])
                ->whereBetween('age_min', [$agMin - 6, $agMax])
                ->where(function ($q) use ($slot) {
                    $q->where('title', 'like', '%'.$slot['keyword'].'%')
                        ->orWhere('cognitive_domain', $slot['cognitive_domain'] ?? null);
                })
                ->inRandomOrder()
                ->first();

            if (! $activity) {
                // Fallback: any activity of matching subject and age range
                $activity = Activity::where('subject', $slot['subject'])
                    ->whereBetween('age_min', [$agMin - 6, $agMax])
                    ->inRandomOrder()
                    ->first();
            }

            if ($activity) {
                ThemeActivity::create([
                    'weekly_theme_id' => $week->id,
                    'activity_id' => $activity->id,
                    'subject_slot' => $slot['subject'],
                    'day_of_week' => $slot['day'],
                    'time_of_day' => $slot['time'],
                    'sort_order' => $slot['order'],
                ]);
            }
        }
    }

    private function journeyData(): array
    {
        return [
            // ─── PRESCHOOL JOURNEYS ────────────────────────────────────────
            [
                'title' => 'The Ocean',
                'description' => 'Dive deep into the wonder of the ocean! Children explore water, marine life, colours of the sea, and the beauty of our blue planet through science, art, maths, and movement.',
                'age_tier' => 'preschool',
                'emoji' => '🌊',
                'cover_color' => '#0EA5E9',
                'sort_order' => 1,
                'weeks' => [
                    [
                        'theme' => 'The Blue World',
                        'description' => 'Introduce children to the concept of the ocean as a world of water',
                        'emoji' => '💧',
                        'big_idea' => 'Water covers most of our planet and is home to incredible life',
                        'activities' => [
                            ['subject' => 'numeracy',       'keyword' => 'count',  'cognitive_domain' => 'attention',         'day' => 1, 'time' => 'morning',   'order' => 1],
                            ['subject' => 'sensory',        'keyword' => 'water',  'cognitive_domain' => 'sensory',           'day' => 1, 'time' => 'afternoon', 'order' => 2],
                            ['subject' => 'science',        'keyword' => 'float',  'cognitive_domain' => 'pattern_recognition', 'day' => 2, 'time' => 'morning',   'order' => 3],
                            ['subject' => 'language',       'keyword' => 'ocean',  'cognitive_domain' => 'language',          'day' => 2, 'time' => 'afternoon', 'order' => 4],
                            ['subject' => 'creative_arts',  'keyword' => 'blue',   'cognitive_domain' => 'spatial_reasoning', 'day' => 3, 'time' => 'morning',   'order' => 5],
                            ['subject' => 'music',          'keyword' => 'sound',  'cognitive_domain' => 'attention',         'day' => 3, 'time' => 'afternoon', 'order' => 6],
                            ['subject' => 'fine_motor',     'keyword' => 'fish',   'cognitive_domain' => 'spatial_reasoning', 'day' => 4, 'time' => 'morning',   'order' => 7],
                            ['subject' => 'social',         'keyword' => 'share',  'cognitive_domain' => 'inhibitory_control', 'day' => 4, 'time' => 'afternoon', 'order' => 8],
                            ['subject' => 'cognitive',      'keyword' => 'memory', 'cognitive_domain' => 'working_memory',    'day' => 5, 'time' => 'morning',   'order' => 9],
                            ['subject' => 'creative_arts',  'keyword' => 'draw',   'cognitive_domain' => 'spatial_reasoning', 'day' => 5, 'time' => 'afternoon', 'order' => 10],
                        ],
                    ],
                    [
                        'theme' => 'Sea Creatures',
                        'description' => 'Meet the amazing animals that live in the ocean',
                        'emoji' => '🐠',
                        'big_idea' => 'Thousands of different animals call the ocean home',
                        'activities' => [
                            ['subject' => 'science',        'keyword' => 'animal', 'cognitive_domain' => 'pattern_recognition', 'day' => 1, 'time' => 'morning',   'order' => 1],
                            ['subject' => 'creative_arts',  'keyword' => 'color',  'cognitive_domain' => 'spatial_reasoning', 'day' => 1, 'time' => 'afternoon', 'order' => 2],
                            ['subject' => 'numeracy',       'keyword' => 'sort',   'cognitive_domain' => 'cognitive_flexibility', 'day' => 2, 'time' => 'morning',   'order' => 3],
                            ['subject' => 'language',       'keyword' => 'story',  'cognitive_domain' => 'working_memory',    'day' => 2, 'time' => 'afternoon', 'order' => 4],
                            ['subject' => 'gross_motor',    'keyword' => 'swim',   'cognitive_domain' => 'attention',         'day' => 3, 'time' => 'morning',   'order' => 5],
                            ['subject' => 'sensory',        'keyword' => 'touch',  'cognitive_domain' => 'sensory',           'day' => 3, 'time' => 'afternoon', 'order' => 6],
                            ['subject' => 'cognitive',      'keyword' => 'match',  'cognitive_domain' => 'working_memory',    'day' => 4, 'time' => 'morning',   'order' => 7],
                            ['subject' => 'social',         'keyword' => 'team',   'cognitive_domain' => 'inhibitory_control', 'day' => 4, 'time' => 'afternoon', 'order' => 8],
                            ['subject' => 'science',        'keyword' => 'observe', 'cognitive_domain' => 'attention',         'day' => 5, 'time' => 'morning',   'order' => 9],
                            ['subject' => 'creative_arts',  'keyword' => 'craft',  'cognitive_domain' => 'spatial_reasoning', 'day' => 5, 'time' => 'afternoon', 'order' => 10],
                        ],
                    ],
                    [
                        'theme' => 'Water & Weather',
                        'description' => 'Explore how the ocean affects our weather and climate',
                        'emoji' => '🌧️',
                        'big_idea' => 'The ocean and weather are connected in a giant cycle',
                        'activities' => [
                            ['subject' => 'science',       'keyword' => 'rain',    'cognitive_domain' => 'sequential_thinking', 'day' => 1, 'time' => 'morning',   'order' => 1],
                            ['subject' => 'sensory',       'keyword' => 'rain',    'cognitive_domain' => 'sensory',           'day' => 1, 'time' => 'afternoon', 'order' => 2],
                            ['subject' => 'numeracy',      'keyword' => 'measure', 'cognitive_domain' => 'attention',         'day' => 2, 'time' => 'morning',   'order' => 3],
                            ['subject' => 'language',      'keyword' => 'cloud',   'cognitive_domain' => 'language',          'day' => 2, 'time' => 'afternoon', 'order' => 4],
                            ['subject' => 'creative_arts', 'keyword' => 'paint',   'cognitive_domain' => 'spatial_reasoning', 'day' => 3, 'time' => 'morning',   'order' => 5],
                            ['subject' => 'gross_motor',   'keyword' => 'move',    'cognitive_domain' => 'attention',         'day' => 3, 'time' => 'afternoon', 'order' => 6],
                            ['subject' => 'science',       'keyword' => 'cycle',   'cognitive_domain' => 'sequential_thinking', 'day' => 4, 'time' => 'morning',   'order' => 7],
                            ['subject' => 'social',        'keyword' => 'discuss', 'cognitive_domain' => 'working_memory',    'day' => 4, 'time' => 'afternoon', 'order' => 8],
                            ['subject' => 'cognitive',     'keyword' => 'pattern', 'cognitive_domain' => 'pattern_recognition', 'day' => 5, 'time' => 'morning',   'order' => 9],
                            ['subject' => 'creative_arts', 'keyword' => 'journal', 'cognitive_domain' => 'metacognition',     'day' => 5, 'time' => 'afternoon', 'order' => 10],
                        ],
                    ],
                    [
                        'theme' => 'Ocean Helpers',
                        'description' => 'Discover how we can protect and care for the ocean',
                        'emoji' => '♻️',
                        'big_idea' => 'Everyone can help keep our oceans healthy',
                        'activities' => [
                            ['subject' => 'social',        'keyword' => 'help',    'cognitive_domain' => 'inhibitory_control', 'day' => 1, 'time' => 'morning',   'order' => 1],
                            ['subject' => 'science',       'keyword' => 'clean',   'cognitive_domain' => 'attention',         'day' => 1, 'time' => 'afternoon', 'order' => 2],
                            ['subject' => 'language',      'keyword' => 'recycle', 'cognitive_domain' => 'language',          'day' => 2, 'time' => 'morning',   'order' => 3],
                            ['subject' => 'creative_arts', 'keyword' => 'poster',  'cognitive_domain' => 'spatial_reasoning', 'day' => 2, 'time' => 'afternoon', 'order' => 4],
                            ['subject' => 'numeracy',      'keyword' => 'count',   'cognitive_domain' => 'attention',         'day' => 3, 'time' => 'morning',   'order' => 5],
                            ['subject' => 'gross_motor',   'keyword' => 'clean',   'cognitive_domain' => 'attention',         'day' => 3, 'time' => 'afternoon', 'order' => 6],
                            ['subject' => 'cognitive',     'keyword' => 'solve',   'cognitive_domain' => 'cognitive_flexibility', 'day' => 4, 'time' => 'morning',   'order' => 7],
                            ['subject' => 'social',        'keyword' => 'share',   'cognitive_domain' => 'inhibitory_control', 'day' => 4, 'time' => 'afternoon', 'order' => 8],
                            ['subject' => 'science',       'keyword' => 'future',  'cognitive_domain' => 'metacognition',     'day' => 5, 'time' => 'morning',   'order' => 9],
                            ['subject' => 'creative_arts', 'keyword' => 'create',  'cognitive_domain' => 'spatial_reasoning', 'day' => 5, 'time' => 'afternoon', 'order' => 10],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'The Garden',
                'description' => 'Children become junior gardeners! Explore plants, seeds, insects, and the cycle of life through hands-on science, sensory play, and creative arts.',
                'age_tier' => 'preschool',
                'emoji' => '🌱',
                'cover_color' => '#22C55E',
                'sort_order' => 2,
                'weeks' => [
                    ['theme' => 'Seeds & Growth', 'description' => 'How do seeds become plants?', 'emoji' => '🌰', 'big_idea' => 'Every plant starts as a tiny seed', 'activities' => [
                        ['subject' => 'science',       'keyword' => 'seed',   'cognitive_domain' => 'sequential_thinking', 'day' => 1, 'time' => 'morning',   'order' => 1],
                        ['subject' => 'sensory',       'keyword' => 'soil',   'cognitive_domain' => 'sensory',            'day' => 1, 'time' => 'afternoon', 'order' => 2],
                        ['subject' => 'numeracy',      'keyword' => 'count',  'cognitive_domain' => 'attention',          'day' => 2, 'time' => 'morning',   'order' => 3],
                        ['subject' => 'language',      'keyword' => 'plant',  'cognitive_domain' => 'language',           'day' => 2, 'time' => 'afternoon', 'order' => 4],
                        ['subject' => 'fine_motor',    'keyword' => 'plant',  'cognitive_domain' => 'spatial_reasoning',  'day' => 3, 'time' => 'morning',   'order' => 5],
                        ['subject' => 'creative_arts', 'keyword' => 'draw',   'cognitive_domain' => 'spatial_reasoning',  'day' => 3, 'time' => 'afternoon', 'order' => 6],
                        ['subject' => 'science',       'keyword' => 'water',  'cognitive_domain' => 'attention',          'day' => 4, 'time' => 'morning',   'order' => 7],
                        ['subject' => 'social',        'keyword' => 'care',   'cognitive_domain' => 'inhibitory_control', 'day' => 4, 'time' => 'afternoon', 'order' => 8],
                        ['subject' => 'cognitive',     'keyword' => 'order',  'cognitive_domain' => 'sequential_thinking', 'day' => 5, 'time' => 'morning',   'order' => 9],
                        ['subject' => 'creative_arts', 'keyword' => 'color',  'cognitive_domain' => 'spatial_reasoning',  'day' => 5, 'time' => 'afternoon', 'order' => 10],
                    ]],
                    ['theme' => 'Garden Insects', 'description' => 'Who lives in the garden?', 'emoji' => '🐛', 'big_idea' => 'Insects are essential helpers in our garden', 'activities' => [
                        ['subject' => 'science',       'keyword' => 'insect', 'cognitive_domain' => 'pattern_recognition', 'day' => 1, 'time' => 'morning',   'order' => 1],
                        ['subject' => 'creative_arts', 'keyword' => 'bug',    'cognitive_domain' => 'spatial_reasoning',  'day' => 1, 'time' => 'afternoon', 'order' => 2],
                        ['subject' => 'language',      'keyword' => 'bee',    'cognitive_domain' => 'language',           'day' => 2, 'time' => 'morning',   'order' => 3],
                        ['subject' => 'numeracy',      'keyword' => 'count',  'cognitive_domain' => 'attention',          'day' => 2, 'time' => 'afternoon', 'order' => 4],
                        ['subject' => 'gross_motor',   'keyword' => 'crawl',  'cognitive_domain' => 'attention',          'day' => 3, 'time' => 'morning',   'order' => 5],
                        ['subject' => 'sensory',       'keyword' => 'touch',  'cognitive_domain' => 'sensory',            'day' => 3, 'time' => 'afternoon', 'order' => 6],
                        ['subject' => 'cognitive',     'keyword' => 'match',  'cognitive_domain' => 'working_memory',     'day' => 4, 'time' => 'morning',   'order' => 7],
                        ['subject' => 'social',        'keyword' => 'share',  'cognitive_domain' => 'inhibitory_control', 'day' => 4, 'time' => 'afternoon', 'order' => 8],
                        ['subject' => 'science',       'keyword' => 'observe', 'cognitive_domain' => 'attention',          'day' => 5, 'time' => 'morning',   'order' => 9],
                        ['subject' => 'creative_arts', 'keyword' => 'make',   'cognitive_domain' => 'spatial_reasoning',  'day' => 5, 'time' => 'afternoon', 'order' => 10],
                    ]],
                    ['theme' => 'Fruits & Vegetables', 'description' => 'Discovering what the garden gives us', 'emoji' => '🍅', 'big_idea' => 'Gardens give us healthy food to nourish our bodies', 'activities' => [
                        ['subject' => 'science',       'keyword' => 'fruit',  'cognitive_domain' => 'pattern_recognition', 'day' => 1, 'time' => 'morning',   'order' => 1],
                        ['subject' => 'numeracy',      'keyword' => 'sort',   'cognitive_domain' => 'cognitive_flexibility', 'day' => 1, 'time' => 'afternoon', 'order' => 2],
                        ['subject' => 'sensory',       'keyword' => 'taste',  'cognitive_domain' => 'sensory',            'day' => 2, 'time' => 'morning',   'order' => 3],
                        ['subject' => 'language',      'keyword' => 'recipe', 'cognitive_domain' => 'sequential_thinking', 'day' => 2, 'time' => 'afternoon', 'order' => 4],
                        ['subject' => 'fine_motor',    'keyword' => 'cut',    'cognitive_domain' => 'spatial_reasoning',  'day' => 3, 'time' => 'morning',   'order' => 5],
                        ['subject' => 'creative_arts', 'keyword' => 'print',  'cognitive_domain' => 'spatial_reasoning',  'day' => 3, 'time' => 'afternoon', 'order' => 6],
                        ['subject' => 'numeracy',      'keyword' => 'graph',  'cognitive_domain' => 'pattern_recognition', 'day' => 4, 'time' => 'morning',   'order' => 7],
                        ['subject' => 'social',        'keyword' => 'cook',   'cognitive_domain' => 'working_memory',     'day' => 4, 'time' => 'afternoon', 'order' => 8],
                        ['subject' => 'science',       'keyword' => 'grow',   'cognitive_domain' => 'sequential_thinking', 'day' => 5, 'time' => 'morning',   'order' => 9],
                        ['subject' => 'creative_arts', 'keyword' => 'stamp',  'cognitive_domain' => 'spatial_reasoning',  'day' => 5, 'time' => 'afternoon', 'order' => 10],
                    ]],
                    ['theme' => 'Garden Through the Seasons', 'description' => 'How does the garden change?', 'emoji' => '🍂', 'big_idea' => 'Gardens change with every season — a never-ending story', 'activities' => [
                        ['subject' => 'science',       'keyword' => 'season', 'cognitive_domain' => 'sequential_thinking', 'day' => 1, 'time' => 'morning',   'order' => 1],
                        ['subject' => 'creative_arts', 'keyword' => 'season', 'cognitive_domain' => 'spatial_reasoning',  'day' => 1, 'time' => 'afternoon', 'order' => 2],
                        ['subject' => 'language',      'keyword' => 'change', 'cognitive_domain' => 'language',           'day' => 2, 'time' => 'morning',   'order' => 3],
                        ['subject' => 'numeracy',      'keyword' => 'pattern', 'cognitive_domain' => 'pattern_recognition', 'day' => 2, 'time' => 'afternoon', 'order' => 4],
                        ['subject' => 'sensory',       'keyword' => 'nature', 'cognitive_domain' => 'sensory',            'day' => 3, 'time' => 'morning',   'order' => 5],
                        ['subject' => 'gross_motor',   'keyword' => 'garden', 'cognitive_domain' => 'attention',          'day' => 3, 'time' => 'afternoon', 'order' => 6],
                        ['subject' => 'cognitive',     'keyword' => 'change', 'cognitive_domain' => 'cognitive_flexibility', 'day' => 4, 'time' => 'morning',   'order' => 7],
                        ['subject' => 'social',        'keyword' => 'help',   'cognitive_domain' => 'inhibitory_control', 'day' => 4, 'time' => 'afternoon', 'order' => 8],
                        ['subject' => 'science',       'keyword' => 'review', 'cognitive_domain' => 'metacognition',      'day' => 5, 'time' => 'morning',   'order' => 9],
                        ['subject' => 'creative_arts', 'keyword' => 'book',   'cognitive_domain' => 'sequential_thinking', 'day' => 5, 'time' => 'afternoon', 'order' => 10],
                    ]],
                ],
            ],
            [
                'title' => 'My Body',
                'description' => 'A wonderful self-discovery journey! Children learn about their amazing body — senses, emotions, movement, nutrition, and how to stay healthy.',
                'age_tier' => 'preschool',
                'emoji' => '🧠',
                'cover_color' => '#EC4899',
                'sort_order' => 3,
                'weeks' => [
                    ['theme' => 'My Five Senses', 'description' => 'Discover your incredible sensory powers', 'emoji' => '👁️', 'big_idea' => 'Your body has five amazing tools for exploring the world', 'activities' => $this->simpleWeekSlots(['sensory', 'science', 'language', 'creative_arts', 'cognitive'])],
                    ['theme' => 'How I Move', 'description' => 'Celebrate what your body can do', 'emoji' => '🏃', 'big_idea' => 'Your body is designed to move in amazing ways', 'activities' => $this->simpleWeekSlots(['gross_motor', 'fine_motor', 'numeracy', 'music', 'social'])],
                    ['theme' => 'Feelings & Emotions', 'description' => 'Name, understand, and manage big feelings', 'emoji' => '❤️', 'big_idea' => 'All feelings are okay — we can learn to understand them', 'activities' => $this->simpleWeekSlots(['social', 'language', 'creative_arts', 'emotional_regulation', 'cognitive'])],
                    ['theme' => 'Healthy Habits', 'description' => 'Build habits that keep the body strong', 'emoji' => '🥦', 'big_idea' => 'Small healthy habits every day keep our bodies happy', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'social', 'creative_arts', 'gross_motor'])],
                ],
            ],
            [
                'title' => 'Space & Stars',
                'description' => 'Blast off on a cosmic adventure! Explore the sun, moon, stars, planets, and the wonder of space through science experiments, maths, and creative arts.',
                'age_tier' => 'preschool',
                'emoji' => '🚀',
                'cover_color' => '#6366F1',
                'sort_order' => 4,
                'weeks' => [
                    ['theme' => 'Sun, Moon & Earth', 'description' => 'Day, night, and our place in space', 'emoji' => '🌙', 'big_idea' => 'The Sun gives us light and the moon lights our nights', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'language', 'creative_arts', 'cognitive'])],
                    ['theme' => 'The Planets', 'description' => 'Meet the planets in our solar system', 'emoji' => '🪐', 'big_idea' => 'Eight very different planets travel around our Sun', 'activities' => $this->simpleWeekSlots(['science', 'creative_arts', 'numeracy', 'language', 'cognitive'])],
                    ['theme' => 'Stars & Constellations', 'description' => 'Connect the dots in the night sky', 'emoji' => '⭐', 'big_idea' => 'Stars form patterns — and people have told stories about them for thousands of years', 'activities' => $this->simpleWeekSlots(['science', 'creative_arts', 'language', 'numeracy', 'cognitive'])],
                    ['theme' => 'Space Explorers', 'description' => 'Rockets, astronauts, and imagining space travel', 'emoji' => '👩‍🚀', 'big_idea' => 'Curiosity and courage push us to explore the unknown', 'activities' => $this->simpleWeekSlots(['science', 'gross_motor', 'creative_arts', 'social', 'cognitive'])],
                ],
            ],
            [
                'title' => 'Animals Around Us',
                'description' => 'From pets to wild animals and jungle creatures — children explore animal habitats, behaviours, sounds, and how we share the planet with the animal kingdom.',
                'age_tier' => 'preschool',
                'emoji' => '🦁',
                'cover_color' => '#F59E0B',
                'sort_order' => 5,
                'weeks' => [
                    ['theme' => 'Pet Friends', 'description' => 'Animals we live with and care for', 'emoji' => '🐶', 'big_idea' => 'Pets teach us responsibility, empathy, and love', 'activities' => $this->simpleWeekSlots(['social', 'science', 'language', 'creative_arts', 'numeracy'])],
                    ['theme' => 'Farm Animals', 'description' => 'Animals that feed and clothe us', 'emoji' => '🐄', 'big_idea' => 'Farm animals work alongside people and give us food and materials', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'social', 'creative_arts', 'language'])],
                    ['theme' => 'Wild Animals', 'description' => 'Creatures in forests, deserts, and jungles', 'emoji' => '🐘', 'big_idea' => 'Wild animals are perfectly adapted for their environment', 'activities' => $this->simpleWeekSlots(['science', 'language', 'creative_arts', 'cognitive', 'gross_motor'])],
                    ['theme' => 'Ocean Animals', 'description' => 'Creatures of the deep blue', 'emoji' => '🐋', 'big_idea' => 'The ocean holds the largest animals on Earth', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'creative_arts', 'language', 'sensory'])],
                ],
            ],
            [
                'title' => 'My Community',
                'description' => 'Who are the helpers in our community? Children learn about community roles, buildings, rules, and what it means to be a good neighbour and global citizen.',
                'age_tier' => 'preschool',
                'emoji' => '🏘️',
                'cover_color' => '#F97316',
                'sort_order' => 6,
                'weeks' => [
                    ['theme' => 'Community Helpers', 'description' => 'People who make our community work', 'emoji' => '👨‍⚕️', 'big_idea' => 'Every community depends on many people doing important jobs', 'activities' => $this->simpleWeekSlots(['social', 'language', 'creative_arts', 'numeracy', 'cognitive'])],
                    ['theme' => 'Buildings & Places', 'description' => 'Where do we go in our community?', 'emoji' => '🏛️', 'big_idea' => 'Every building in our community serves a purpose', 'activities' => $this->simpleWeekSlots(['social', 'creative_arts', 'numeracy', 'language', 'science'])],
                    ['theme' => 'Rules & Responsibility', 'description' => 'Why do communities have rules?', 'emoji' => '📋', 'big_idea' => 'Rules help everyone live together safely and fairly', 'activities' => $this->simpleWeekSlots(['social', 'language', 'cognitive', 'creative_arts', 'numeracy'])],
                    ['theme' => 'Celebrations & Culture', 'description' => 'How do communities celebrate?', 'emoji' => '🎉', 'big_idea' => 'Celebrations bring communities together and connect us to our history', 'activities' => $this->simpleWeekSlots(['social', 'creative_arts', 'music', 'language', 'numeracy'])],
                ],
            ],
            // ─── TODDLER JOURNEYS ─────────────────────────────────────────
            [
                'title' => 'Touch & Feel',
                'description' => 'A rich sensory journey for toddlers! Explore textures, temperatures, and sensations through safe, hands-on sensory play that builds brain connections.',
                'age_tier' => 'toddler',
                'emoji' => '✋',
                'cover_color' => '#14B8A6',
                'sort_order' => 1,
                'weeks' => [
                    ['theme' => 'Soft & Hard', 'description' => 'Explore contrasting textures', 'emoji' => '🪨', 'big_idea' => 'Different textures tell us about the world around us', 'activities' => $this->simpleWeekSlots(['sensory', 'language', 'fine_motor', 'creative_arts', 'cognitive'])],
                    ['theme' => 'Wet & Dry', 'description' => 'Water play and dry sand exploration', 'emoji' => '💦', 'big_idea' => 'Water and dry materials feel and act very differently', 'activities' => $this->simpleWeekSlots(['sensory', 'science', 'language', 'creative_arts', 'numeracy'])],
                    ['theme' => 'Warm & Cold', 'description' => 'Discover temperature through safe exploration', 'emoji' => '❄️', 'big_idea' => 'Our sense of touch tells us about temperature', 'activities' => $this->simpleWeekSlots(['sensory', 'science', 'language', 'fine_motor', 'cognitive'])],
                    ['theme' => 'All My Senses', 'description' => 'A multi-sensory celebration week', 'emoji' => '🌟', 'big_idea' => 'All five senses work together to help us understand our world', 'activities' => $this->simpleWeekSlots(['sensory', 'language', 'creative_arts', 'social', 'cognitive'])],
                ],
            ],
            [
                'title' => 'Big & Small',
                'description' => 'Toddlers discover size, shape, and spatial concepts through stacking, sorting, fitting, and exploring. A foundational maths and science journey.',
                'age_tier' => 'toddler',
                'emoji' => '📐',
                'cover_color' => '#8B5CF6',
                'sort_order' => 2,
                'weeks' => [
                    ['theme' => 'Big & Small', 'description' => 'Comparing sizes in the world around us', 'emoji' => '🐘', 'big_idea' => 'Everything has a size — and we can compare them!', 'activities' => $this->simpleWeekSlots(['numeracy', 'sensory', 'language', 'creative_arts', 'cognitive'])],
                    ['theme' => 'Shapes Everywhere', 'description' => 'Finding shapes in everyday objects', 'emoji' => '🔷', 'big_idea' => 'Shapes are the building blocks of our world', 'activities' => $this->simpleWeekSlots(['numeracy', 'creative_arts', 'language', 'cognitive', 'fine_motor'])],
                    ['theme' => 'Tall & Short', 'description' => 'Exploring height and length', 'emoji' => '📏', 'big_idea' => 'We can measure and compare everything around us', 'activities' => $this->simpleWeekSlots(['numeracy', 'gross_motor', 'language', 'science', 'creative_arts'])],
                    ['theme' => 'Full & Empty', 'description' => 'Capacity and container play', 'emoji' => '🥛', 'big_idea' => 'Containers can hold different amounts — and we can measure them!', 'activities' => $this->simpleWeekSlots(['numeracy', 'sensory', 'language', 'science', 'fine_motor'])],
                ],
            ],
            [
                'title' => 'Colors Everywhere',
                'description' => 'An immersive color discovery journey for toddlers! Learn color names, mixing, and matching through art, nature walks, and playful activities.',
                'age_tier' => 'toddler',
                'emoji' => '🎨',
                'cover_color' => '#EC4899',
                'sort_order' => 3,
                'weeks' => [
                    ['theme' => 'Red, Yellow & Blue', 'description' => 'The three primary colours', 'emoji' => '🔴', 'big_idea' => 'Red, yellow, and blue are the parent colours — all other colours come from them', 'activities' => $this->simpleWeekSlots(['creative_arts', 'sensory', 'language', 'science', 'fine_motor'])],
                    ['theme' => 'Green, Orange & Purple', 'description' => 'Secondary colours through mixing', 'emoji' => '🟢', 'big_idea' => 'When we mix two colours, a brand new colour appears like magic!', 'activities' => $this->simpleWeekSlots(['creative_arts', 'science', 'language', 'sensory', 'cognitive'])],
                    ['theme' => 'Colors in Nature', 'description' => 'Colour hunts in the natural world', 'emoji' => '🌈', 'big_idea' => 'Nature is the most colorful artist of all', 'activities' => $this->simpleWeekSlots(['science', 'creative_arts', 'language', 'gross_motor', 'sensory'])],
                    ['theme' => 'My Color Rainbow', 'description' => 'Celebrating all the colours we know', 'emoji' => '🌟', 'big_idea' => 'Together, all colours make something beautiful', 'activities' => $this->simpleWeekSlots(['creative_arts', 'numeracy', 'language', 'social', 'cognitive'])],
                ],
            ],
            [
                'title' => 'Animal Friends',
                'description' => 'Toddlers meet friendly animals through simple stories, sounds, and movement. Builds early vocabulary, empathy, and curiosity about the natural world.',
                'age_tier' => 'toddler',
                'emoji' => '🐾',
                'cover_color' => '#78716C',
                'sort_order' => 4,
                'weeks' => [
                    ['theme' => 'What Does the Animal Say?', 'description' => 'Animal sounds and names', 'emoji' => '🐮', 'big_idea' => 'Animals communicate in their own special ways', 'activities' => $this->simpleWeekSlots(['language', 'sensory', 'creative_arts', 'gross_motor', 'cognitive'])],
                    ['theme' => 'Baby Animals', 'description' => 'Discovering baby animal names', 'emoji' => '🐣', 'big_idea' => 'Every animal was once a baby — just like you!', 'activities' => $this->simpleWeekSlots(['language', 'creative_arts', 'social', 'numeracy', 'science'])],
                    ['theme' => 'Animals on the Farm', 'description' => 'Familiar farm animals and what they give us', 'emoji' => '🐖', 'big_idea' => 'Farm animals are our helpers', 'activities' => $this->simpleWeekSlots(['social', 'science', 'creative_arts', 'language', 'numeracy'])],
                    ['theme' => 'Animal Movements', 'description' => 'Move like your favourite animal', 'emoji' => '🏊', 'big_idea' => 'Animals move in amazing ways — hop, swim, fly, slither!', 'activities' => $this->simpleWeekSlots(['gross_motor', 'language', 'creative_arts', 'social', 'cognitive'])],
                ],
            ],
            // ─── SCHOOL JOURNEYS ──────────────────────────────────────────
            [
                'title' => 'Ecosystems',
                'description' => 'An in-depth scientific investigation of ecosystems, food webs, biodiversity, and environmental impact — connecting maths, science, writing, and geography.',
                'age_tier' => 'school',
                'emoji' => '🌿',
                'cover_color' => '#16A34A',
                'sort_order' => 1,
                'weeks' => [
                    ['theme' => 'What Is an Ecosystem?', 'description' => 'Introduction to ecosystems and habitats', 'emoji' => '🌍', 'big_idea' => 'An ecosystem is a community of living things and their environment', 'activities' => $this->simpleWeekSlots(['science', 'language', 'numeracy', 'creative_arts', 'cognitive'])],
                    ['theme' => 'Food Webs & Food Chains', 'description' => 'Who eats whom in an ecosystem?', 'emoji' => '🦅', 'big_idea' => 'Energy flows through ecosystems in food chains and food webs', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'language', 'creative_arts', 'cognitive'])],
                    ['theme' => 'Biodiversity', 'description' => 'The incredible variety of life on Earth', 'emoji' => '🦋', 'big_idea' => 'Biodiversity is essential for a healthy planet', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'language', 'creative_arts', 'social'])],
                    ['theme' => 'Human Impact', 'description' => 'How do we affect our ecosystems?', 'emoji' => '♻️', 'big_idea' => 'Our choices affect ecosystems — we can choose to protect them', 'activities' => $this->simpleWeekSlots(['science', 'social', 'language', 'creative_arts', 'cognitive'])],
                ],
            ],
            [
                'title' => 'Simple Machines',
                'description' => 'Children become young engineers! Explore six simple machines — lever, wheel and axle, pulley, inclined plane, wedge, and screw — through hands-on building challenges.',
                'age_tier' => 'school',
                'emoji' => '⚙️',
                'cover_color' => '#475569',
                'sort_order' => 2,
                'weeks' => [
                    ['theme' => 'Levers & Pulleys', 'description' => 'How do we lift heavy things?', 'emoji' => '🏗️', 'big_idea' => 'Simple machines multiply our force — we can do more with less effort', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'creative_arts', 'language', 'cognitive'])],
                    ['theme' => 'Wheels & Inclined Planes', 'description' => 'Rolling and ramps change everything', 'emoji' => '🛞', 'big_idea' => 'Wheels and ramps are two of humanity\'s greatest inventions', 'activities' => $this->simpleWeekSlots(['science', 'numeracy', 'creative_arts', 'language', 'cognitive'])],
                    ['theme' => 'Build a Machine', 'description' => 'Combine simple machines into complex ones', 'emoji' => '🔩', 'big_idea' => 'Combining simple machines creates compound machines that do amazing things', 'activities' => $this->simpleWeekSlots(['science', 'creative_arts', 'numeracy', 'language', 'social'])],
                    ['theme' => 'Machines Around Us', 'description' => 'Spot simple machines in everyday life', 'emoji' => '🔍', 'big_idea' => 'Simple machines are hidden everywhere — once you know where to look!', 'activities' => $this->simpleWeekSlots(['science', 'language', 'numeracy', 'creative_arts', 'cognitive'])],
                ],
            ],
            [
                'title' => 'Numbers in Nature',
                'description' => 'Discover mathematics in the natural world — Fibonacci sequences in sunflowers, symmetry in snowflakes, patterns in honeycombs, and geometry in crystals.',
                'age_tier' => 'school',
                'emoji' => '🔢',
                'cover_color' => '#0891B2',
                'sort_order' => 3,
                'weeks' => [
                    ['theme' => 'Patterns & Sequences', 'description' => 'Finding number patterns in nature', 'emoji' => '🌻', 'big_idea' => 'The Fibonacci sequence appears again and again in the natural world', 'activities' => $this->simpleWeekSlots(['numeracy', 'science', 'creative_arts', 'language', 'cognitive'])],
                    ['theme' => 'Symmetry & Geometry', 'description' => 'Shapes and symmetry in the natural world', 'emoji' => '❄️', 'big_idea' => 'Nature uses geometry to build beautiful, efficient structures', 'activities' => $this->simpleWeekSlots(['numeracy', 'science', 'creative_arts', 'language', 'cognitive'])],
                    ['theme' => 'Measurement & Scale', 'description' => 'How big? How fast? How heavy?', 'emoji' => '📏', 'big_idea' => 'Measurement helps us understand and compare the world', 'activities' => $this->simpleWeekSlots(['numeracy', 'science', 'creative_arts', 'language', 'cognitive'])],
                    ['theme' => 'Data & Graphs', 'description' => 'Collecting and displaying nature data', 'emoji' => '📊', 'big_idea' => 'Data helps us understand and explain patterns in the world', 'activities' => $this->simpleWeekSlots(['numeracy', 'science', 'creative_arts', 'language', 'social'])],
                ],
            ],
            [
                'title' => 'World Cultures',
                'description' => 'A respectful, curious exploration of world cultures — food, music, art, celebrations, and stories from different countries. Building global citizenship and empathy.',
                'age_tier' => 'school',
                'emoji' => '🌏',
                'cover_color' => '#9333EA',
                'sort_order' => 4,
                'weeks' => [
                    ['theme' => 'Asia', 'description' => 'Exploring the rich cultures of Asia', 'emoji' => '🏯', 'big_idea' => 'Asia is the largest continent and home to diverse, ancient civilisations', 'activities' => $this->simpleWeekSlots(['social', 'language', 'creative_arts', 'science', 'numeracy'])],
                    ['theme' => 'Africa', 'description' => 'The incredible cultures of the African continent', 'emoji' => '🌍', 'big_idea' => 'Africa is the birthplace of humanity and home to extraordinary diversity', 'activities' => $this->simpleWeekSlots(['social', 'language', 'creative_arts', 'science', 'music'])],
                    ['theme' => 'The Americas', 'description' => 'Indigenous and modern cultures of the Americas', 'emoji' => '🦅', 'big_idea' => 'The Americas have been home to remarkable civilisations for thousands of years', 'activities' => $this->simpleWeekSlots(['social', 'language', 'creative_arts', 'science', 'numeracy'])],
                    ['theme' => 'Our Muslim Heritage', 'description' => 'Islamic contributions to civilisation', 'emoji' => '🕌', 'big_idea' => 'Islamic scholars preserved and advanced knowledge in mathematics, astronomy, medicine, and the arts', 'activities' => $this->simpleWeekSlots(['social', 'language', 'creative_arts', 'science', 'numeracy'])],
                ],
            ],
        ];
    }

    /**
     * Generate 10 simple activity slot definitions for a week from 5 subjects.
     * Days 1-5, morning and afternoon.
     */
    private function simpleWeekSlots(array $subjects): array
    {
        $slots = [];
        $order = 1;
        for ($day = 1; $day <= 5; $day++) {
            $subjectIdx = ($day - 1) % count($subjects);
            foreach (['morning', 'afternoon'] as $time) {
                $altIdx = ($time === 'afternoon') ? (($subjectIdx + 1) % count($subjects)) : $subjectIdx;
                $slots[] = [
                    'subject' => $subjects[$altIdx],
                    'keyword' => '',
                    'cognitive_domain' => null,
                    'day' => $day,
                    'time' => $time,
                    'order' => $order++,
                ];
            }
        }

        return $slots;
    }
}
