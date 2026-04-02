<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MilestoneWallController extends Controller
{
    public function index(Request $request)
    {
        $domain = $request->query('domain');

        $achievements = \App\Models\ChildProfile::join('child_achievements', 'child_profiles.id', '=', 'child_achievements.child_profile_id')
            ->join('milestones', function ($join) {
                $join->on('child_achievements.achievable_id', '=', 'milestones.id')
                     ->where('child_achievements.achievable_type', '=', \App\Models\Milestone::class);
            })
            ->when($domain, fn ($q) => $q->where('milestones.domain', $domain))
            ->select('child_achievements.*')
            ->orderByDesc('child_achievements.achieved_at')
            ->paginate(18)
            ->withQueryString();

        // Eager load achievable and child
        $achievements->getCollection()->transform(function ($item) {
            $item->achievable = \App\Models\Milestone::find($item->achievable_id);
            $item->child = \App\Models\ChildProfile::find($item->child_profile_id);
            return $item;
        });

        return view('milestones.wall', compact('achievements'));
    }
}
