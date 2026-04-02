<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Services\MilestoneService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly MilestoneService $milestoneService) {}

    public function show(Request $request, ChildProfile $child)
    {
        $this->authorize('view', $child);

        $ageMonths = $child->age_months ?? 0;

        // Pick 3 age-appropriate activities for today (rotate daily by date seed)
        $todayActivities = $child->appropriateActivities()
            ->inRandomOrder()
            ->seed(today()->timestamp)
            ->limit(3)
            ->get()
            ->each(function ($activity) use ($child) {
                $progress = ChildActivityProgress::where('child_profile_id', $child->id)
                    ->where('activity_id', $activity->id)
                    ->first();
                $activity->setAttribute('pivot', (object) ['completed' => (bool) ($progress->completed ?? false)]);
            });

        $totalCompleted = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
            ->count();

        $badgeCount = $child->achievements()
            ->where('achievable_type', \App\Models\Badge::class)
            ->count();

        $nextTargets = $this->milestoneService->nextTargets($child);
        $nextMilestone = $nextTargets->first();

        // Auto-evaluate milestones in the background (fire and forget)
        // In production this would dispatch to a queue
        try {
            $this->milestoneService->evaluate($child);
        } catch (\Throwable) {
            // Non-critical — don't break the dashboard
        }

        return view('child.dashboard', compact(
            'child', 'todayActivities', 'totalCompleted', 'badgeCount', 'nextMilestone'
        ));
    }
}
