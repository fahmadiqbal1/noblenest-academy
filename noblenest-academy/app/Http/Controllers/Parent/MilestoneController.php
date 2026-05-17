<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use App\Models\Milestone;

class MilestoneController extends Controller
{
    public function index(ChildProfile $child)
    {
        $this->authorize('view', $child);

        // Milestones appropriate for this child's age, joined with completion status
        $milestones = Milestone::where('age_months_min', '<=', $child->age_months ?? 0)
            ->where('age_months_max', '>=', ($child->age_months ?? 0) - 12)
            ->orderBy('age_months_min')
            ->get()
            ->map(function ($milestone) use ($child) {
                $milestone->completed = $child->milestones()
                    ->where('milestone_id', $milestone->id)
                    ->wherePivot('is_completed', true)
                    ->exists();

                return $milestone;
            });

        return view('parent.milestones', compact('child', 'milestones'));
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
