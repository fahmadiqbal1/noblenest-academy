<?php

namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->practitionerProfile;

        $reviewedCount = $profile->reviews()->count();
        $approvedCount = $profile->reviews()->where('decision', 'approved')->count();

        $pendingQueue = MaternalContent::where('moderation_status', 'pending')
            ->whereDoesntHave('reviews', function ($q) use ($profile) {
                $q->where('practitioner_profile_id', $profile->id);
            })
            ->count();

        return view('practitioner.dashboard', compact('profile', 'reviewedCount', 'approvedCount', 'pendingQueue'));
    }
}
