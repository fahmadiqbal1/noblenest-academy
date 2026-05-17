<?php

namespace App\Services;

use App\Models\Activity;

class CurriculumHealthService
{
    /** Age range covered by the curriculum (years). */
    protected array $ageRange;

    /** Required skills/subjects that every age should have. */
    protected array $requiredSkills = [
        'Language & Literacy', 'Numeracy', 'Cognitive', 'Fine Motor',
        'Gross Motor', 'Social-Emotional', 'Creative Arts', 'STEM',
        // Phase 5: Executive Functioning & Cognitive Development
        'Executive Function',       // Working memory, inhibitory control, cognitive flexibility
        'Emotional Regulation',     // Naming feelings, calming techniques, self-regulation
        'Mental Arithmetic',        // Soroban, flash math, number sense
        'Focus & Attention',        // Calligraphy drills, sustained attention tasks
    ];

    /** Minimum activities per age × skill cell. */
    protected int $target = 5;

    public function __construct()
    {
        $this->ageRange = range(0, 10);
    }

    /**
     * Return every age × skill cell that is below the coverage target.
     *
     * @return array<int, array{age: int, skill: string, count: int, target: int}>
     */
    public function getGaps(): array
    {
        $gaps = [];

        foreach ($this->ageRange as $age) {
            foreach ($this->requiredSkills as $skill) {
                $count = Activity::where('subject', $skill)
                    ->where('age_min', '<=', $age)
                    ->where('age_max', '>=', $age)
                    ->count();

                if ($count < $this->target) {
                    $gaps[] = [
                        'age' => $age,
                        'skill' => $skill,
                        'count' => $count,
                        'target' => $this->target,
                    ];
                }
            }
        }

        return $gaps;
    }

    /**
     * Calculate overall curriculum health as a percentage.
     */
    public function getHealthScore(): float
    {
        $totalCells = count($this->ageRange) * count($this->requiredSkills);
        $coveredCells = $totalCells - count($this->getGaps());

        return $totalCells > 0
            ? round(($coveredCells / $totalCells) * 100, 1)
            : 0;
    }

    /**
     * Human-readable gap report for CLI / email output.
     */
    public function getGapReport(): string
    {
        $gaps = $this->getGaps();
        $score = $this->getHealthScore();

        if (empty($gaps)) {
            return "Curriculum health: {$score}% — all age × skill cells are covered.";
        }

        $lines = ["Curriculum health: {$score}%", str_repeat('─', 50)];

        foreach ($gaps as $gap) {
            $lines[] = sprintf(
                'Age %2d │ %-22s │ %d / %d activities',
                $gap['age'], $gap['skill'], $gap['count'], $gap['target'],
            );
        }

        $lines[] = str_repeat('─', 50);
        $lines[] = count($gaps).' gap(s) found. Run curriculum:auto-generate to fill them.';

        return implode("\n", $lines);
    }
}
