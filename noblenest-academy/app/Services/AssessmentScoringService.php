<?php

namespace App\Services;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;

/**
 * Phase 3 — discovery battery scoring.
 *
 * Takes a flat answer vector ([sequence => option_index]) and produces:
 *   - per-dimension totals across the 6 axes (cognitive_logic, creative,
 *     social, kinetic, naturalist, linguistic)
 *   - top 2 strengths
 *   - an "interest cluster" label keyed off the strongest axis
 *
 * NOT a clinical assessment. The PDF report (Phase 3 follow-up) repeats
 * that framing at the top of every page.
 */
class AssessmentScoringService
{
    public const DIMENSIONS = [
        'cognitive_logic', 'creative', 'social',
        'kinetic',         'naturalist', 'linguistic',
    ];

    private const CLUSTER_LABELS = [
        'cognitive_logic' => 'Problem-solver',
        'creative'        => 'Maker / artist',
        'social'          => 'Connector',
        'kinetic'         => 'Mover / athlete',
        'naturalist'      => 'Naturalist / explorer',
        'linguistic'      => 'Storyteller / writer',
    ];

    /**
     * @param  array<int, int>  $answers  ['sequence' => 'option_index']
     * @return array{scores: array<string,int>, top: array<int,string>, cluster: string}
     */
    public function score(array $answers, string $battery = 'discovery'): array
    {
        $scores = array_fill_keys(self::DIMENSIONS, 0);

        $questions = AssessmentQuestion::query()
            ->where('battery', $battery)
            ->orderBy('sequence')
            ->get()
            ->keyBy('sequence');

        foreach ($answers as $sequence => $optionIdx) {
            $q = $questions->get((int) $sequence);
            if (! $q) continue;
            $options = $q->options;
            $option = $options[(int) $optionIdx] ?? null;
            if (! $option || ! isset($option['dimensions'])) continue;
            foreach ($option['dimensions'] as $dim => $points) {
                if (isset($scores[$dim])) {
                    $scores[$dim] += (int) $points;
                }
            }
        }

        arsort($scores);
        $top = array_slice(array_keys($scores), 0, 2);
        $cluster = self::CLUSTER_LABELS[$top[0]] ?? 'Curious mind';

        return compact('scores', 'top', 'cluster');
    }

    public function persist(?int $childId, ?int $userId, array $answers): AssessmentResponse
    {
        $result = $this->score($answers);
        return AssessmentResponse::create([
            'child_id'     => $childId,
            'user_id'      => $userId,
            'battery'      => 'discovery',
            'answers'      => $answers,
            'scores'       => $result['scores'],
            'completed_at' => now(),
        ]);
    }
}
