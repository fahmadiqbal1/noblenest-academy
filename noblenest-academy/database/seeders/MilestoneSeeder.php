<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
            // 0–12 months — baby
            ['title' => 'Tracks moving objects with eyes',       'domain' => 'cognitive',  'age_months_min' => 0,  'age_months_max' => 3],
            ['title' => 'Responds to sounds',                    'domain' => 'language',   'age_months_min' => 0,  'age_months_max' => 3],
            ['title' => 'Smiles responsively',                   'domain' => 'social',     'age_months_min' => 1,  'age_months_max' => 4],
            ['title' => 'Holds head up when on tummy',           'domain' => 'motor',      'age_months_min' => 2,  'age_months_max' => 5],
            ['title' => 'Laughs out loud',                       'domain' => 'social',     'age_months_min' => 3,  'age_months_max' => 5],
            ['title' => 'Reaches for objects',                   'domain' => 'motor',      'age_months_min' => 3,  'age_months_max' => 6],
            ['title' => 'Babbles (da-da, ba-ba)',                'domain' => 'language',   'age_months_min' => 4,  'age_months_max' => 8],
            ['title' => 'Sits without support',                  'domain' => 'motor',      'age_months_min' => 4,  'age_months_max' => 8],
            ['title' => 'Waves bye-bye',                         'domain' => 'social',     'age_months_min' => 7,  'age_months_max' => 11],
            ['title' => 'Pulls to stand',                        'domain' => 'motor',      'age_months_min' => 8,  'age_months_max' => 12],
            ['title' => 'Says first word',                       'domain' => 'language',   'age_months_min' => 9,  'age_months_max' => 14],
            ['title' => 'Walks independently',                   'domain' => 'motor',      'age_months_min' => 10, 'age_months_max' => 15],
            // 12–24 months — toddler early
            ['title' => 'Uses 5–10 words',                      'domain' => 'language',   'age_months_min' => 12, 'age_months_max' => 18],
            ['title' => 'Points to objects of interest',         'domain' => 'cognitive',  'age_months_min' => 12, 'age_months_max' => 18],
            ['title' => 'Scribbles with crayon',                 'domain' => 'motor',      'age_months_min' => 12, 'age_months_max' => 20],
            ['title' => 'Feeds self with spoon',                 'domain' => 'motor',      'age_months_min' => 13, 'age_months_max' => 20],
            ['title' => 'Imitates household activities',         'domain' => 'social',     'age_months_min' => 14, 'age_months_max' => 22],
            ['title' => 'Uses 2-word phrases',                   'domain' => 'language',   'age_months_min' => 18, 'age_months_max' => 24],
            ['title' => 'Sorts shapes and colours',              'domain' => 'cognitive',  'age_months_min' => 18, 'age_months_max' => 28],
            // 24–48 months — toddler / preschool bridge
            ['title' => 'Uses 3-word sentences',                 'domain' => 'language',   'age_months_min' => 24, 'age_months_max' => 36],
            ['title' => 'Jumps with both feet',                  'domain' => 'motor',      'age_months_min' => 24, 'age_months_max' => 36],
            ['title' => 'Names familiar animals',                'domain' => 'cognitive',  'age_months_min' => 24, 'age_months_max' => 36],
            ['title' => 'Engages in pretend play',               'domain' => 'social',     'age_months_min' => 24, 'age_months_max' => 42],
            ['title' => 'Understands "same" and "different"',    'domain' => 'cognitive',  'age_months_min' => 30, 'age_months_max' => 42],
            ['title' => 'Counts to 10',                          'domain' => 'cognitive',  'age_months_min' => 36, 'age_months_max' => 54],
            // 48–72 months — preschool
            ['title' => 'Draws a person with 3+ body parts',     'domain' => 'motor',      'age_months_min' => 48, 'age_months_max' => 60],
            ['title' => 'Recognises own name in writing',        'domain' => 'literacy',   'age_months_min' => 48, 'age_months_max' => 66],
            ['title' => 'Follows 3-step instruction',            'domain' => 'cognitive',  'age_months_min' => 48, 'age_months_max' => 60],
            ['title' => 'Shares and takes turns',                'domain' => 'social',     'age_months_min' => 48, 'age_months_max' => 66],
            ['title' => 'Writes first name',                     'domain' => 'literacy',   'age_months_min' => 54, 'age_months_max' => 72],
            // 72–120 months — school age
            ['title' => 'Reads simple words',                    'domain' => 'literacy',   'age_months_min' => 60, 'age_months_max' => 84],
            ['title' => 'Adds single-digit numbers',             'domain' => 'numeracy',   'age_months_min' => 60, 'age_months_max' => 84],
            ['title' => 'Solves simple puzzles independently',   'domain' => 'cognitive',  'age_months_min' => 72, 'age_months_max' => 96],
            ['title' => 'Shows empathy to peers',                'domain' => 'social',     'age_months_min' => 72, 'age_months_max' => 96],
            ['title' => 'Reads fluently',                        'domain' => 'literacy',   'age_months_min' => 84, 'age_months_max' => 120],
            ['title' => 'Multiplies and divides',                'domain' => 'numeracy',   'age_months_min' => 96, 'age_months_max' => 120],

            // ── Phase 5: Executive Functioning ──────────────────────
            ['title' => 'Holds 2 items in memory (e.g. "get the ball and the cup")',
                'domain' => 'executive_function', 'age_months_min' => 18, 'age_months_max' => 36],
            ['title' => 'Waits for a short turn during a game',
                'domain' => 'executive_function', 'age_months_min' => 24, 'age_months_max' => 42],
            ['title' => 'Stops an action when told (inhibitory control)',
                'domain' => 'executive_function', 'age_months_min' => 30, 'age_months_max' => 48],
            ['title' => 'Switches between two simple tasks',
                'domain' => 'executive_function', 'age_months_min' => 36, 'age_months_max' => 60],
            ['title' => 'Plans and executes a 3-step project independently',
                'domain' => 'executive_function', 'age_months_min' => 60, 'age_months_max' => 96],

            // ── Phase 5: Emotional Regulation ───────────────────────
            ['title' => 'Names 4 basic emotions (happy, sad, angry, scared)',
                'domain' => 'emotional_regulation', 'age_months_min' => 24, 'age_months_max' => 42],
            ['title' => 'Uses a calming strategy when upset (deep breaths, counting)',
                'domain' => 'emotional_regulation', 'age_months_min' => 30, 'age_months_max' => 54],
            ['title' => 'Recognises emotions in others\' faces',
                'domain' => 'emotional_regulation', 'age_months_min' => 36, 'age_months_max' => 60],
            ['title' => 'Expresses frustration with words instead of actions',
                'domain' => 'emotional_regulation', 'age_months_min' => 42, 'age_months_max' => 72],

            // ── Phase 5: Mental Arithmetic ──────────────────────────
            ['title' => 'Instantly recognises quantities up to 5 (subitising)',
                'domain' => 'mental_arithmetic', 'age_months_min' => 36, 'age_months_max' => 60],
            ['title' => 'Adds single-digit numbers mentally (no fingers)',
                'domain' => 'mental_arithmetic', 'age_months_min' => 60, 'age_months_max' => 84],
            ['title' => 'Completes soroban-style addition within 10',
                'domain' => 'mental_arithmetic', 'age_months_min' => 72, 'age_months_max' => 108],

            // ── Phase 5: Focus & Attention ──────────────────────────
            ['title' => 'Sustains focus on a 5-minute activity without prompting',
                'domain' => 'focus_attention', 'age_months_min' => 36, 'age_months_max' => 54],
            ['title' => 'Completes a 15-minute focused task independently',
                'domain' => 'focus_attention', 'age_months_min' => 60, 'age_months_max' => 84],
            ['title' => 'Switches attention between tasks without losing progress',
                'domain' => 'focus_attention', 'age_months_min' => 72, 'age_months_max' => 108],
        ];

        foreach ($milestones as $idx => $milestone) {
            Milestone::updateOrCreate(
                ['title' => $milestone['title']],
                array_merge($milestone, ['sort_order' => $idx])
            );
        }
    }
}
