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
     * Only the parent who owns the child can view the card.
     */
    public function show(ShareCard $shareCard)
    {
        $shareCard->loadMissing('childProfile');

        // Only the owning parent (or admin) may view a child's share card
        $user = Auth::user();
        abort_unless(
            $user && ($user->role === 'admin' || $shareCard->childProfile->parent_id === $user->id),
            403
        );

        return view('share.card', compact('shareCard'));
    }

    /**
     * List all share cards for the authenticated user's children.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $childIds = $user->childProfiles()->pluck('id');

        $cards = ShareCard::with(['childProfile', 'activity', 'badge'])
            ->whereIn('child_profile_id', $childIds)
            ->latest()
            ->paginate(24);

        return view('parent.share-cards', compact('cards'));
    }
}
