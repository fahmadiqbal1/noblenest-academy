<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Models\MaternalEmergencySign;
use App\Services\MaternalContentFilterService;
use Illuminate\Support\Facades\Auth;

class JourneyController extends Controller
{
    public function __construct(private MaternalContentFilterService $filter) {}

    public function index()
    {
        $profile = Auth::user()->maternalProfile;

        // Build a week-by-week timeline with content counts
        $currentWeek = $profile->current_week;
        $weeks = [];

        for ($w = 1; $w <= 42; $w++) {
            $weeks[$w] = [
                'week'       => $w,
                'is_current' => $w === $currentWeek,
                'is_past'    => $w < $currentWeek,
            ];
        }

        return view('maternal.journey.index', compact('profile', 'weeks', 'currentWeek'));
    }

    public function week(int $week)
    {
        $profile = Auth::user()->maternalProfile;

        abort_if($week < 1 || $week > 52, 404);

        // Determine stage for this week
        $content = $this->filter->safeContentQuery($profile)
            ->where(function ($q) use ($week) {
                $q->where('recommended_week_start', '<=', $week)
                  ->where(function ($q2) use ($week) {
                      $q2->where('recommended_week_end', '>=', $week)
                         ->orWhereNull('recommended_week_end');
                  });
            })
            ->orderBy('sort_order')
            ->get();

        $emergencySigns = MaternalEmergencySign::where('stage', $profile->stage)->get();

        return view('maternal.journey.week', compact('profile', 'week', 'content', 'emergencySigns'));
    }
}
