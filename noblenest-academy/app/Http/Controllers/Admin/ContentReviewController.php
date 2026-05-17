<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ContentReviewController extends Controller
{
    /**
     * List activities pending review.
     */
    public function index(Request $request)
    {
        $activities = Activity::where('published', false)
            ->when($request->subject, fn ($q, $s) => $q->where('subject', $s))
            ->when($request->age_tier, fn ($q, $t) => $q->where('age_tier', $t))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.content-review.index', compact('activities'));
    }

    /**
     * Approve (publish) a single activity.
     */
    public function approve(Activity $activity)
    {
        $activity->update(['published' => true]);

        return back()->with('status', '"'.$activity->title.'" published.');
    }

    /**
     * Reject (soft-delete) a pending activity.
     */
    public function reject(Activity $activity)
    {
        $activity->delete();

        return back()->with('status', 'Activity rejected and removed.');
    }

    /**
     * Approve all pending activities in bulk.
     */
    public function approveAll()
    {
        $count = Activity::where('published', false)->update(['published' => true]);

        return back()->with('status', "{$count} activities approved.");
    }
}
