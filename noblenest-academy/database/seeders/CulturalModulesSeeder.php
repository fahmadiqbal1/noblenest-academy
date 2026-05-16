<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityStep;
use Illuminate\Database\Seeder;

/**
 * Phase 3 — cross-cultural pedagogy starter activities.
 *
 * Three example activities per culture (Japanese, Chinese, Scandinavian,
 * Islamic) tagged via the `cultural_*` subjects. The curriculum team extends
 * each set to a full module (~20 activities per culture) using
 * `php artisan content:generate database/seed-data/cultural-{culture}.csv`.
 *
 * Pedagogical sources for each set (per master prompt §2):
 *   japanese    → empathy + group, martial-arts-inspired balance, respect for elders/nature
 *   chinese     → structured arts (ink painting, calligraphy), group singing, pinyin
 *   scandinavian→ outdoor + nature, cooperative games, democratic circle time
 *   islamic     → Quran appreciation, Arabic letters, daily manners (gated by child.is_muslim)
 */
class CulturalModulesSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            'cultural_japanese'    => $this->japanese(),
            'cultural_chinese'     => $this->chinese(),
            'cultural_scandinavian'=> $this->scandinavian(),
            'cultural_islamic'     => $this->islamic(),
        ];

        foreach ($sets as $subject => $activities) {
            foreach ($activities as $row) {
                $this->seedActivity($subject, $row);
            }
        }
    }

    private function seedActivity(string $subject, array $row): void
    {
        $payload = array_merge([
            'subject'          => $subject,
            'language'         => 'en',
            'is_free'          => true,
            'emoji'            => '🌏',
            'duration_minutes' => 15,
        ], $row);

        $steps = $row['steps'] ?? [];
        unset($payload['steps']);

        $activity = Activity::query()->updateOrCreate(
            ['title' => $payload['title'], 'subject' => $subject, 'age_min' => $payload['age_min']],
            $payload
        );

        foreach ($steps as $n => $step) {
            ActivityStep::updateOrCreate(
                ['activity_id' => $activity->id, 'step_number' => $n + 1],
                ['title' => $step[0], 'instruction' => $step[1]]
            );
        }
    }

    private function japanese(): array
    {
        return [
            [
                'title' => 'Class Pet Round',
                'description' => 'Take turns checking on a class pet or plant — Japanese yochien-style empathy practice.',
                'age_min' => 36, 'age_max' => 60,
                'activity_type' => 'routine',
                'emoji' => '🐹',
                'benefit_explanation' => 'Builds empathy and shared responsibility through small group caretaking.',
                'steps' => [
                    ['Greet', 'Greet the pet or plant gently.'],
                    ['Check + care', 'Refill water or check leaves.'],
                    ['Report', 'Tell the group one thing you noticed.'],
                ],
            ],
            [
                'title' => 'Bow Greeting',
                'description' => 'Practice the traditional Japanese bow and respectful greeting.',
                'age_min' => 36, 'age_max' => 72,
                'activity_type' => 'social',
                'emoji' => '🙇',
                'benefit_explanation' => 'Cultural respect, body awareness, and ritualised greeting.',
                'steps' => [
                    ['Stand', 'Stand tall with hands at your sides.'],
                    ['Bow slowly', 'Bow from the waist; say "konnichiwa".'],
                    ['Rise + smile', 'Stand back up and smile at your partner.'],
                ],
            ],
            [
                'title' => 'Origami Crane (intro)',
                'description' => 'Folding a single-step origami crane introduction.',
                'age_min' => 60, 'age_max' => 96,
                'activity_type' => 'craft',
                'emoji' => '🪿',
                'benefit_explanation' => 'Fine motor precision and patience; cultural craft heritage.',
                'steps' => [
                    ['Pick paper', 'Pick a square sheet of origami paper.'],
                    ['Fold corner-to-corner', 'Make a triangle by folding diagonally.'],
                    ['Unfold + share', 'Open it up and share what shape you saw.'],
                ],
            ],
        ];
    }

    private function chinese(): array
    {
        return [
            [
                'title' => 'Ink Strokes',
                'description' => 'Practice three basic Chinese calligraphy strokes with a brush.',
                'age_min' => 48, 'age_max' => 96,
                'activity_type' => 'craft',
                'emoji' => '🖌️',
                'benefit_explanation' => 'Brush control, focus, and exposure to Chinese script.',
                'steps' => [
                    ['Horizontal', 'Pull the brush left-to-right slowly.'],
                    ['Vertical', 'Pull the brush top-to-bottom.'],
                    ['Dot', 'Press and lift to make a single dot.'],
                ],
            ],
            [
                'title' => 'Pinyin First Sounds',
                'description' => 'Three Mandarin tones with the syllable "ma".',
                'age_min' => 36, 'age_max' => 72,
                'activity_type' => 'vocal',
                'emoji' => '🎵',
                'benefit_explanation' => 'Tonal listening + early Mandarin phonetics.',
                'steps' => [
                    ['First tone', 'Say "mā" — flat and high.'],
                    ['Third tone', 'Say "mǎ" — dipping low then up.'],
                    ['Fourth tone', 'Say "mà" — sharp falling.'],
                ],
            ],
            [
                'title' => 'Group Singing Circle',
                'description' => 'Sit in a circle and learn a short Mandarin nursery rhyme.',
                'age_min' => 36, 'age_max' => 84,
                'activity_type' => 'song',
                'emoji' => '🎤',
                'benefit_explanation' => 'Common to Chinese kindergartens — rhythm + group belonging.',
                'steps' => [
                    ['Circle up', 'Sit cross-legged in a circle.'],
                    ['Sing slowly', 'Sing one verse phrase by phrase.'],
                    ['Repeat together', 'Sing the verse again as a group.'],
                ],
            ],
        ];
    }

    private function scandinavian(): array
    {
        return [
            [
                'title' => 'Nature Scavenger Hunt',
                'description' => 'Collect five items from nature — leaves, pebbles, sticks — and sort them.',
                'age_min' => 36, 'age_max' => 84,
                'activity_type' => 'outdoor',
                'emoji' => '🍃',
                'benefit_explanation' => 'Outdoor exploration and classification, central to Nordic pedagogy.',
                'steps' => [
                    ['Set the hunt', 'Decide what kinds of things to collect.'],
                    ['Hunt', 'Walk outside together collecting carefully.'],
                    ['Sort + name', 'Sit down and group the finds by type.'],
                ],
            ],
            [
                'title' => 'Democratic Circle Time',
                'description' => 'Children take turns naming today\'s activity choice.',
                'age_min' => 48, 'age_max' => 96,
                'activity_type' => 'discussion',
                'emoji' => '🪑',
                'benefit_explanation' => 'Voice + agency + listening — the Nordic democratic model.',
                'steps' => [
                    ['Sit in a circle', 'Everyone sits at the same height.'],
                    ['Speaking stone', 'Whoever holds the stone speaks; others listen.'],
                    ['Vote', 'Vote with thumbs up/down on each suggestion.'],
                ],
            ],
            [
                'title' => 'Cooperative Stone Stack',
                'description' => 'Build a stone tower as a group — every child places one.',
                'age_min' => 48, 'age_max' => 96,
                'activity_type' => 'outdoor',
                'emoji' => '🪨',
                'benefit_explanation' => 'Cooperation, fine balance, shared joy in a built outcome.',
                'steps' => [
                    ['Find stones', 'Collect 8-10 flat stones.'],
                    ['Take turns', 'Each child places one stone carefully.'],
                    ['Cheer when it falls', 'Celebrate the topple — try again together.'],
                ],
            ],
        ];
    }

    private function islamic(): array
    {
        return [
            [
                'title' => 'Alif Trace',
                'description' => 'Trace the Arabic letter ا (alif) right-to-left.',
                'age_min' => 36, 'age_max' => 84,
                'activity_type' => 'tracing',
                'emoji' => '✒️',
                'benefit_explanation' => 'First Arabic letter recognition + RTL stroke order.',
                'steps' => [
                    ['Watch', 'Watch the guide trace the alif slowly.'],
                    ['Trace', 'Trace inside the guide with a finger or pencil.'],
                    ['Name it', 'Say "alif" out loud.'],
                ],
            ],
            [
                'title' => 'Bismillah Before Eating',
                'description' => 'Practice saying "Bismillah" before a snack or meal.',
                'age_min' => 24, 'age_max' => 72,
                'activity_type' => 'routine',
                'emoji' => '🤲',
                'benefit_explanation' => 'Daily ritual that ties faith to ordinary moments.',
                'steps' => [
                    ['Pause', 'Put hands together before reaching for food.'],
                    ['Say it', 'Say "Bismillah" softly.'],
                    ['Eat slowly', 'Take the first bite mindfully.'],
                ],
            ],
            [
                'title' => 'Short Sura Listen',
                'description' => 'Listen to a short, age-appropriate Qur\'an recitation together.',
                'age_min' => 36, 'age_max' => 96,
                'activity_type' => 'reading',
                'emoji' => '📖',
                'benefit_explanation' => 'Familiarity with recitation cadence and family faith practice.',
                'steps' => [
                    ['Sit quietly', 'Sit somewhere calm.'],
                    ['Listen', 'Play a short recitation; close eyes if it helps.'],
                    ['Share', 'Talk about how the sound felt.'],
                ],
            ],
        ];
    }
}
