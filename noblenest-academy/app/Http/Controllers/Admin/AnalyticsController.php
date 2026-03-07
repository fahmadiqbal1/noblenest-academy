<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        [$coverage, $topLiked, $totalSkills, $totalActivities, $monthlyCompletions] = $this->buildData();

        return view('admin.analytics.index', compact(
            'coverage', 'topLiked', 'totalSkills', 'totalActivities', 'monthlyCompletions'
        ));
    }

    public function reportEmail(Request $request)
    {
        [$coverage, $topLiked, $totalSkills, $totalActivities] = $this->buildData();

        try {
            Mail::send(
                'admin.activities.report_email',
                compact('coverage', 'topLiked', 'totalSkills', 'totalActivities'),
                function ($m) {
                    $m->to(config('mail.admin_address', 'admin@noblenest.com'))
                      ->subject('Monthly Curriculum Analytics Report');
                }
            );
            return back()->with('status', 'Report email queued successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send report: ' . $e->getMessage());
        }
    }

    public function mostLiked(Request $request)
    {
        $topLiked = Activity::withCount('likes')->orderByDesc('likes_count')->take(10)->get();

        return response()->json($topLiked->map(fn ($a) => [
            'id'       => $a->id,
            'title'    => $a->title,
            'skill'    => $a->skill,
            'age_min'  => $a->age_min,
            'age_max'  => $a->age_max,
            'likes'    => $a->likes_count,
        ]));
    }

    public function monthlyCompletions(Request $request)
    {
        $rows = \DB::table('activity_user_progress')
            ->selectRaw("strftime('%Y-%m', completed_at) as month, count(*) as completions")
            ->whereNotNull('completed_at')
            ->groupByRaw("strftime('%Y-%m', completed_at)")
            ->orderBy('month')
            ->get();

        return response()->json($rows);
    }

    // ---------------------------------------------------------------

    private function buildData(): array
    {
        $skills = Activity::select('skill')->distinct()->pluck('skill')->filter();

        $coverage = [];
        foreach ($skills as $skill) {
            $activities = Activity::where('skill', $skill)->get();
            $ages       = $activities->flatMap(fn ($a) => [$a->age_min, $a->age_max]);
            $coverage[] = [
                'skill'    => $skill,
                'count'    => $activities->count(),
                'age_min'  => $ages->min(),
                'age_max'  => $ages->max(),
            ];
        }

        $topLiked = collect(); // no likes relation yet; placeholder

        $totalSkills      = $skills->count();
        $totalActivities  = Activity::count();

        $monthlyCompletions = \DB::table('activity_user_progress')
            ->selectRaw("strftime('%Y-%m', completed_at) as month, count(*) as completions")
            ->whereNotNull('completed_at')
            ->groupByRaw("strftime('%Y-%m', completed_at)")
            ->orderBy('month')
            ->get();

        return [$coverage, $topLiked, $totalSkills, $totalActivities, $monthlyCompletions];
    }
}
