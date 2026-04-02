<?php

namespace App\Http\Controllers;

use App\Models\ShareCard;
use App\Models\ChildProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareCardController extends Controller
{
    /**
     * Show a share card (public-facing SSR for OG tags).
     */
    public function show(ShareCard $shareCard)
    {
        return view('share.card', compact('shareCard'));
    }

    /**
     * List all share cards for the authenticated user's children.
     */
    public function index(Request $request)
    {
        $childIds = Auth::user()
            ->childProfiles()
            ->pluck('id');

        $cards = ShareCard::with(['childProfile', 'activity', 'badge'])
            ->whereIn('child_profile_id', $childIds)
            ->latest()
            ->paginate(24);

        return view('parent.share-cards', compact('cards'));
    }
}
