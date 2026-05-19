<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MilestoneWallController extends Controller
{
    /**
     * Public "wall of achievements" page.
     *
     * The persistence side (a polymorphic `child_achievements` log written
     * when a child completes a milestone) is not yet built — the prior
     * controller queried `child_achievements` directly and 500'd in
     * production ("Base table not found"). Until that table + writer are
     * shipped, this controller returns an empty paginator so the page
     * renders its built-in empty state instead of crashing. Tracked in
     * docs/QA_FINDINGS.md as M1.
     */
    public function index(Request $request)
    {
        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $achievements = new LengthAwarePaginator(
            new Collection,
            0,
            18,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('milestones.wall', compact('achievements'));
    }
}
