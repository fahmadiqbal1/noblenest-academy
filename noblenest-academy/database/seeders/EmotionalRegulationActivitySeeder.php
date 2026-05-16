<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class EmotionalRegulationActivitySeeder extends Seeder
{
    /**
     * Seed emotional regulation activities across all age tiers.
     * ~40 activities covering: breathing, naming feelings, co-regulation, transitions, impulse control.
     */
    public function run(): void
    {
        $activities = [
            // BABY (0-24 months) - 8 activities
            ['title' => 'Calm Breathing with Bubbles', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Gentle Lullaby Time', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Soft Touch & Cuddle Rituals', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Baby Massage & Deep Pressure', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Soothing Sensory Bottle', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Slow Movement Dance', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Quiet Music Listening', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Co-Regulation with Parent', 'age_tier' => 'baby', 'difficulty' => 'easy', 'mess_level' => 'low'],

            // TODDLER (2-3 years) - 10 activities
            ['title' => 'Belly Breathing with Stuffed Animal', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Naming Feelings with Emotions Poster', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Calm-Down Corner Setup', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Deep Pressure Squeeze Game', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'medium'],
            ['title' => 'Transition Warning Song', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Slow Stretching Routine', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Feelings Chart Check-In', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Sensory Play with Playdough', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'medium'],
            ['title' => 'Co-Regulation Breathing', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Calming Music & Movement', 'age_tier' => 'toddler', 'difficulty' => 'easy', 'mess_level' => 'low'],

            // PRESCHOOL (3-5 years) - 11 activities
            ['title' => 'Box Breathing Exercise', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Emotions Detective Game', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Mindfulness Sensory Walk', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Anger Management Scribble', 'age_tier' => 'preschool', 'difficulty' => 'easy', 'mess_level' => 'medium'],
            ['title' => 'Problem-Solving Story Time', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Gratitude Sharing Circle', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Transition Countdown Timer', 'age_tier' => 'preschool', 'difficulty' => 'easy', 'mess_level' => 'low'],
            ['title' => 'Yoga Poses for Calm', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Feelings in My Body Map', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Calming Collage Making', 'age_tier' => 'preschool', 'difficulty' => 'easy', 'mess_level' => 'medium'],
            ['title' => 'Positive Self-Talk Mirrors', 'age_tier' => 'preschool', 'difficulty' => 'medium', 'mess_level' => 'low'],

            // SCHOOL (6-8 years) - 11 activities
            ['title' => '4-7-8 Breathing Technique', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Emotion Wheel Labeling', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Mindful Eating Meditation', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Journaling My Feelings', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Conflict Resolution Role-Play', 'age_tier' => 'school', 'difficulty' => 'hard', 'mess_level' => 'low'],
            ['title' => 'Stress Relief Art Project', 'age_tier' => 'school', 'difficulty' => 'easy', 'mess_level' => 'medium'],
            ['title' => 'Future Visualizations', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Strength & Growth Mindset', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Social Skills Role-Play', 'age_tier' => 'school', 'difficulty' => 'hard', 'mess_level' => 'low'],
            ['title' => 'Time-In Strategies', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
            ['title' => 'Kindness & Empathy Challenge', 'age_tier' => 'school', 'difficulty' => 'medium', 'mess_level' => 'low'],
        ];

        foreach ($activities as $data) {
            Activity::firstOrCreate(
                [
                    'title'             => $data['title'],
                    'cognitive_domain'  => 'emotional_regulation',
                ],
                [
                    'description'       => "An emotional regulation activity for {$data['age_tier']} children",
                    'instructions'      => 'Follow the steps to help your child regulate emotions.',
                    'materials_needed'  => null,
                    'duration_minutes'  => 10,
                    'difficulty'        => $data['difficulty'],
                    'age_tier'          => $data['age_tier'],
                    'subject'           => 'emotional_regulation',
                    'language'          => 'english',
                    'is_free'           => true,
                    'mess_level'        => $data['mess_level'],
                    'safety_warnings'   => null,
                    'adaptations'       => json_encode([
                        'easier' => 'Provide more guidance and support.',
                        'harder' => 'Challenge with deeper reflection questions.',
                    ]),
                    'developmental_domains' => json_encode(['social_emotional', 'attention']),
                    'materials_cost'    => 'free',
                    'parent_involvement' => 'guided',
                    'instructions_for_parent' => 'Help your child identify and name their emotions, then practice the regulation technique together.',
                ]
            );
        }

        $this->command->info('EmotionalRegulation activities seeded successfully!');
    }
}
