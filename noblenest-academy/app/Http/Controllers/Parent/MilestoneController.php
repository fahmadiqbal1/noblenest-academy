<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use App\Models\Milestone;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    /**
     * Parent milestones hub.
     *
     * The route `/parent/milestones` has no {child} param, so the prior
     * `index(ChildProfile $child)` signature received an empty Eloquent
     * instance, failed the policy check, and 403'd on every click of the
     * dashboard's "View Milestones" CTA. Redirect to a place that actually
     * shows milestones: the first child's dashboard if one exists, or the
     * add-child flow if not.
     */
    public function index()
    {
        $parent = Auth::user();
        $first = ChildProfile::where('parent_id', $parent->id)->first();

        if (! $first) {
            return redirect()->route('children.create')
                ->with('status', __('Add a child to start tracking milestones.'));
        }

        return redirect()->route('child.dashboard', $first);
    }

    public function toggle(ChildProfile $child, Milestone $milestone)
    {
        $this->authorize('view', $child);

        $existing = $child->milestones()
            ->where('milestone_id', $milestone->id)
            ->first();

        if ($existing) {
            $child->milestones()->updateExistingPivot($milestone->id, [
                'is_completed' => ! $existing->pivot->is_completed,
                'completed_at' => ! $existing->pivot->is_completed ? now() : null,
            ]);
        } else {
            $child->milestones()->attach($milestone->id, [
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        return back()->with('status', 'Milestone updated.');
    }
}
