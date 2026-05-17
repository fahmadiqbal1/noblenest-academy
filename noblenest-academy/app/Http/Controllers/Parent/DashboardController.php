<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $children = ChildProfile::where('parent_id', $user->id)
            ->withCount('activityProgress')
            ->get();

        // Active subscription check
        $subscription = $user->subscriptions()
            ->where('active', true)
            ->where('ends_at', '>', now())
            ->first();

        $hasSubscription = $subscription !== null;

        // Recent activity across all children
        $childIds = $children->pluck('id');
        $recentActivity = ChildActivityProgress::with(['activity', 'childProfile'])
            ->whereIn('child_profile_id', $childIds)
            ->latest('completed_at')
            ->limit(10)
            ->get();

        return view('parent.dashboard', compact('user', 'children', 'hasSubscription', 'subscription', 'recentActivity'));
    }

    /**
     * Show individual child progress summary.
     */
    public function child(ChildProfile $child)
    {
        $this->authorize('view', $child);

        $progress = ChildActivityProgress::with('activity')
            ->where('child_profile_id', $child->id)
            ->latest('completed_at')
            ->paginate(20);

        return view('parent.child', compact('child', 'progress'));
    }
}
