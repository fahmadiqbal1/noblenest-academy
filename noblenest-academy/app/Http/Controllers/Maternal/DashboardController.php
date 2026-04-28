<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Models\MaternalEmergencySign;
use App\Models\MaternalJournal;
use App\Models\MaternalProgress;
use App\Services\MaternalContentFilterService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private MaternalContentFilterService $filter) {}

    public function index()
    {
        $user    = Auth::user();
        $profile = $user->maternalProfile;

        // Content recommendations for current stage
        $recommended = $this->filter->safeContentQuery($profile)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // Progress stats
        $completedCount = MaternalProgress::where('maternal_profile_id', $profile->id)
            ->whereNotNull('completed_at')
            ->count();

        // Recent journal entries
        $recentJournals = MaternalJournal::where('maternal_profile_id', $profile->id)
            ->latest('entry_date')
            ->limit(3)
            ->get();

        // Emergency signs for current stage
        $emergencySigns = MaternalEmergencySign::where('stage', $profile->stage)
            ->where('severity', 'emergency')
            ->get();

        return view('maternal.dashboard', compact(
            'profile',
            'recommended',
            'completedCount',
            'recentJournals',
            'emergencySigns'
        ));
    }
}
