<?php

namespace App\Jobs;

use App\Mail\DailyDigestMail;
use App\Models\ChildActivityProgress;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDailyDigestJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 60;

    public function handle(): void
    {
        // Only send to parents who have at least one active child
        User::where('role', 'Parent')
            ->has('childProfiles')
            ->chunk(50, function ($parents) {
                foreach ($parents as $parent) {
                    $digest = $this->buildDigest($parent);
                    if ($digest['total_completions'] > 0 || $digest['streak_max'] > 0) {
                        Mail::to($parent->email)->queue(new DailyDigestMail($parent, $digest));
                    }
                }
            });
    }

    private function buildDigest(User $parent): array
    {
        $yesterday = now()->subDay()->startOfDay();
        $endOfYesterday = now()->subDay()->endOfDay();

        $children = $parent->childProfiles;

        $totalCompletions = 0;
        $streakMax = 0;
        $summaries = [];

        foreach ($children as $child) {
            $completed = ChildActivityProgress::where('child_profile_id', $child->id)
                ->whereBetween('updated_at', [$yesterday, $endOfYesterday])
                ->whereNotNull('completed_at')
                ->with('activity:id,title,emoji,subject')
                ->get();

            $streak = $child->streak_days ?? 0;

            $totalCompletions += $completed->count();
            $streakMax = max($streakMax, $streak);

            if ($completed->isNotEmpty()) {
                $summaries[] = [
                    'child_name' => $child->name,
                    'age_display' => $child->age_display,
                    'activities' => $completed->map(fn ($p) => $p->activity)->filter()->values(),
                    'streak' => $streak,
                ];
            }
        }

        return [
            'total_completions' => $totalCompletions,
            'streak_max' => $streakMax,
            'summaries' => $summaries,
        ];
    }
}
