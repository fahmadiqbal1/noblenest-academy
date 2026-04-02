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
            ['slug' => 'first-activity',   'title' => 'First Steps',          'emoji' => '👣', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 1]],
            ['slug' => 'five-activities',   'title' => 'High Five!',           'emoji' => '✋', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 5]],
            ['slug' => 'ten-activities',    'title' => 'Explorer',             'emoji' => '🔍', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 10]],
            ['slug' => 'twenty-five-activities', 'title' => 'Adventurer',     'emoji' => '🗺️', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 25]],
            ['slug' => 'fifty-activities',  'title' => 'Super Learner',        'emoji' => '⭐', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 50]],
            ['slug' => 'hundred-activities','title' => 'Knowledge Hero',       'emoji' => '🏆', 'badge_type' => 'milestone', 'criteria' => ['activities_completed' => 100]],
            // Streak badges
            ['slug' => 'streak-3',  'title' => '3-Day Streak!',   'emoji' => '🔥', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 3]],
            ['slug' => 'streak-7',  'title' => 'Week Warrior',    'emoji' => '🦁', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 7]],
            ['slug' => 'streak-14', 'title' => 'Two-Week Champ',  'emoji' => '🌟', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 14]],
            ['slug' => 'streak-30', 'title' => 'Monthly Master',  'emoji' => '👑', 'badge_type' => 'streak', 'criteria' => ['streak_days' => 30]],
            // Subject badges
            ['slug' => 'language-1',  'title' => 'Word Wizard',   'emoji' => '📖', 'badge_type' => 'subject', 'criteria' => ['subject' => 'language', 'count' => 5]],
            ['slug' => 'math-1',      'title' => 'Number Ninja',  'emoji' => '🔢', 'badge_type' => 'subject', 'criteria' => ['subject' => 'math', 'count' => 5]],
            ['slug' => 'science-1',   'title' => 'Curious Scientist', 'emoji' => '🔬', 'badge_type' => 'subject', 'criteria' => ['subject' => 'science', 'count' => 5]],
            ['slug' => 'art-1',       'title' => 'Creative Star',  'emoji' => '🎨', 'badge_type' => 'subject', 'criteria' => ['subject' => 'art', 'count' => 5]],
            // Special
            ['slug' => 'multilingual', 'title' => 'Polyglot',     'emoji' => '🌍', 'badge_type' => 'special', 'criteria' => ['languages_used' => 2]],
            ['slug' => 'early-bird',   'title' => 'Early Bird',   'emoji' => '🐦', 'badge_type' => 'special', 'criteria' => ['morning_activities' => 5]],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
