<?php

namespace App\Console\Commands;

use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\Activity;
use App\Models\ChildSkillState;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillChildSkillStatesCommand extends Command
{
    protected $signature = 'backfill:child-skill-states {--force : Skip confirmation}';

    protected $description = 'Backfill ChildSkillState table from existing ChildActivityProgress records. Idempotent and safe to re-run.';

    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm(
                'This will backfill ChildSkillState from existing activity progress. Continue?',
                true
            )) {
                return 0;
            }
        }

        $this->info('Starting backfill of ChildSkillState...');

        $childCount = 0;
        $stateCount = 0;

        // Iterate all children
        ChildProfile::chunk(50, function ($children) use (&$childCount, &$stateCount) {
            foreach ($children as $child) {
                $childCount++;

                // Get all completed activities for this child, with their metadata
                $progress = ChildActivityProgress::where('child_profile_id', $child->id)
                    ->whereNotNull('completed_at')
                    ->with('activity')
                    ->get();

                if ($progress->isEmpty()) {
                    continue;
                }

                // Group by (cognitive_domain, developmental_domain) combo
                $grouped = $progress->groupBy(function ($p) {
                    $activity = $p->activity;
                    if (!$activity) {
                        return null;
                    }
                    $cogDomain = $activity->cognitive_domain ?? 'unknown';
                    $devDomains = $activity->developmental_domains ?? ['unknown'];
                    // For simplicity, use first dev domain; in future, create state per dev domain
                    $devDomain = is_array($devDomains) ? ($devDomains[0] ?? 'unknown') : 'unknown';
                    return "{$cogDomain}::{$devDomain}";
                })->filter();

                // Create or update ChildSkillState for each group
                foreach ($grouped as $key => $activities) {
                    [$cogDomain, $devDomain] = explode('::', $key);

                    // Calculate mastery: (# successful / # total)
                    $total = $activities->count();
                    $successful = $activities->filter(fn ($p) => $p->score >= 80 || $p->completed_at)->count();
                    $masteryScore = $total > 0 ? $successful / $total : 0.5;

                    // Calculate success streak (simplification: last 3 activities)
                    $lastThree = $activities->sortBy('completed_at')->reverse()->take(3);
                    $recentSuccess = $lastThree->filter(fn ($p) => $p->score >= 80)->count();
                    $recentStruggle = $lastThree->filter(fn ($p) => $p->score < 50)->count();

                    $stateCount++;

                    ChildSkillState::updateOrCreate(
                        [
                            'child_profile_id'    => $child->id,
                            'cognitive_domain'    => $cogDomain,
                            'developmental_domain' => $devDomain,
                        ],
                        [
                            'ema_score'            => max(0, min(1, $masteryScore)),
                            'ema_confidence'      => min($total / 10, 1.0), // Confidence grows with attempts
                            'streak_success'      => $recentSuccess,
                            'streak_struggle'     => $recentStruggle,
                            'total_attempts'      => $total,
                            'successful_attempts' => $successful,
                            'last_updated'        => now(),
                        ]
                    );
                }
            }
        });

        $this->info("Backfill complete!");
        $this->info("  Children processed: {$childCount}");
        $this->info("  Skill states created/updated: {$stateCount}");

        return 0;
    }
}
