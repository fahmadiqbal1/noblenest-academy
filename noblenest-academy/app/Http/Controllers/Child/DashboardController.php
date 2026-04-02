<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateMilestonesJob;
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
            ->get();

        // Fetch all progress records in ONE query instead of one per activity (fixes N+1)
        $activityIds = $todayActivities->pluck('id');
        $progressMap = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereIn('activity_id', $activityIds)
            ->get()
            ->keyBy('activity_id');

        $todayActivities->each(function ($activity) use ($progressMap) {
            $prog = $progressMap->get($activity->id);
            $activity->setAttribute('pivot', (object) [
                'completed' => $prog && $prog->completed_at !== null,
            ]);
        });

        $totalCompleted = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
            ->count();

        $badgeCount = $child->achievements()
            ->where('achievable_type', \App\Models\Badge::class)
            ->count();

        $nextTargets = $this->milestoneService->nextTargets($child);
        $nextMilestone = $nextTargets->first();

        // Dispatch milestone evaluation to the queue (non-blocking)
        EvaluateMilestonesJob::dispatch($child->id);

        return view('child.dashboard', compact(
            'child', 'todayActivities', 'totalCompleted', 'badgeCount', 'nextMilestone'
        ));
    }
}
