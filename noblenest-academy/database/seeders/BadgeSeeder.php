<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // First steps
            ['slug' => 'first-activity',   'name' => 'First Steps',          'emoji' => '👣', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 1]],
            ['slug' => 'five-activities',   'name' => 'High Five!',           'emoji' => '✋', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 5]],
            ['slug' => 'ten-activities',    'name' => 'Explorer',             'emoji' => '🔍', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 10]],
            ['slug' => 'twenty-five-activities', 'name' => 'Adventurer',     'emoji' => '🗺️', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 25]],
            ['slug' => 'fifty-activities',  'name' => 'Super Learner',        'emoji' => '⭐', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 50]],
            ['slug' => 'hundred-activities','name' => 'Knowledge Hero',       'emoji' => '🏆', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 100]],
            // Streak badges
            ['slug' => 'streak-3',  'name' => '3-Day Streak!',   'emoji' => '🔥', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 3]],
            ['slug' => 'streak-7',  'name' => 'Week Warrior',    'emoji' => '🦁', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 7]],
            ['slug' => 'streak-14', 'name' => 'Two-Week Champ',  'emoji' => '🌟', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 14]],
            ['slug' => 'streak-30', 'name' => 'Monthly Master',  'emoji' => '👑', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 30]],
            // Subject badges
            ['slug' => 'language-1',  'name' => 'Word Wizard',   'emoji' => '📖', 'badge_type' => 'subject', 'criteria' => ['subject' => 'language', 'count' => 5]],
            ['slug' => 'math-1',      'name' => 'Number Ninja',  'emoji' => '🔢', 'badge_type' => 'subject', 'criteria' => ['subject' => 'math', 'count' => 5]],
            ['slug' => 'science-1',   'name' => 'Curious Scientist', 'emoji' => '🔬', 'badge_type' => 'subject', 'criteria' => ['subject' => 'science', 'count' => 5]],
            ['slug' => 'art-1',       'name' => 'Creative Star',  'emoji' => '🎨', 'badge_type' => 'subject', 'criteria' => ['subject' => 'art', 'count' => 5]],
            // Special
            ['slug' => 'multilingual', 'name' => 'Polyglot',     'emoji' => '🌍', 'badge_type' => 'special', 'criteria' => ['languages_used' => 2]],
            ['slug' => 'early-bird',   'name' => 'Early Bird',   'emoji' => '🐦', 'badge_type' => 'special', 'criteria' => ['morning_activities' => 5]],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
