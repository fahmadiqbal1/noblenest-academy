<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use App\Models\ChildActivityProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $children = ChildProfile::where('user_id', $user->id)
            ->withCount('activityProgress')
            ->get();

        // Active subscription check
        $hasSubscription = $user->subscriptions()
            ->where('active', true)
            ->where('ends_at', '>', now())
            ->exists();

        // Recent activity across all children
        $childIds = $children->pluck('id');
        $recentActivity = ChildActivityProgress::with(['activity', 'childProfile'])
            ->whereIn('child_profile_id', $childIds)
            ->latest('completed_at')
            ->limit(10)
            ->get();

        return view('parent.dashboard', compact('user', 'children', 'hasSubscription', 'recentActivity'));
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
