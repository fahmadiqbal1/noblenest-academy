<?php

namespace App\Jobs;

use App\Mail\DailyDigestMail;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\MaternalProgress;
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
                    if ($digest['total_completions'] > 0 || $digest['streak_max'] > 0 || ($digest['maternal']['completed'] ?? 0) > 0) {
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
                    'child_name'  => $child->name,
                    'age_display' => $child->age_display,
                    'activities'  => $completed->map(fn ($p) => $p->activity)->filter()->values(),
                    'streak'      => $streak,
                ];
            }
        }

        return [
            'total_completions' => $totalCompletions,
            'streak_max'        => $streakMax,
            'summaries'         => $summaries,
            'maternal'          => $this->buildMaternalDigest($parent),
        ];
    }

    private function buildMaternalDigest(User $parent): ?array
    {
        if (! config('features.maternal_module')) {
            return null;
        }

        $profile = $parent->maternalProfile;

        if (! $profile || $profile->status !== 'active') {
            return null;
        }

        $yesterday = now()->subDay()->startOfDay();
        $endOfYesterday = now()->subDay()->endOfDay();

        $completedCount = MaternalProgress::where('maternal_profile_id', $profile->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$yesterday, $endOfYesterday])
            ->count();

        return [
            'current_week' => $profile->current_week,
            'trimester'    => $profile->trimester,
            'stage'        => $profile->stage,
            'completed'    => $completedCount,
        ];
    }
}
