<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ChildProfile;
use App\Models\ChildActivityProgress;
use App\Models\ShareCard;
use App\Services\ShareCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildActivityController extends Controller
{
    public function __construct(private readonly ShareCardService $shareCards) {}

    /**
     * List age-appropriate activities for a child.
     */
    public function index(Request $request, ChildProfile $child)
    {
        $this->authorize('view', $child);

        $query = Activity::query()
            ->where('age_min', '<=', $child->age_months / 12)
            ->where('age_max', '>=', $child->age_months / 12)
            ->where('language', $child->preferred_language ?? 'en');

        // Free tier: first 30 activities always accessible
        $completed = ChildActivityProgress::where('child_profile_id', $child->id)
            ->count();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $hasSubscription = $user->subscriptions()
            ->where('active', true)
            ->where('ends_at', '>', now())
            ->exists();

        if (!$hasSubscription && $completed >= 30) {
            // Soft paywall — show activities but mark as locked
            $activities = $query->paginate(12)->through(fn($a) => tap($a, fn($a) => $a->locked = !$a->is_free));
        } else {
            $activities = $query->paginate(12);
        }

        return view('child.activities', compact('child', 'activities', 'hasSubscription'));
    }

    /**
     * Record an activity completion, award badges, generate share card.
     */
    public function complete(Request $request, ChildProfile $child, Activity $activity)
    {
        $this->authorize('view', $child);

        // Idempotent — only record once
        $progress = ChildActivityProgress::firstOrCreate([
            'child_profile_id' => $child->id,
            'activity_id'      => $activity->id,
        ], [
            'completed_at' => now(),
        ]);

        $wasNew = $progress->wasRecentlyCreated;

        if ($wasNew) {
            // Update streak
            $this->updateStreak($child);

            // Generate viral share card on FIRST completion
            $totalCompleted = ChildActivityProgress::where('child_profile_id', $child->id)->count();
            if ($totalCompleted === 1) {
                $imageUrl = $this->shareCards->generateActivityCard($child, $activity);
                $child->update(['share_card_url' => $imageUrl]);

                ShareCard::create([
                    'child_profile_id' => $child->id,
                    'activity_id'      => $activity->id,
                    'card_type'        => 'activity_complete',
                    'image_url'        => $imageUrl,
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'        => true,
                'new'       => $wasNew,
                'share_url' => $child->share_card_url,
            ]);
        }

        return back()->with('status', "Great job! {$child->name} completed {$activity->title}! 🎉");
    }

    private function updateStreak(ChildProfile $child): void
    {
        $lastDate = $child->last_activity_date;
        $today    = now()->toDateString();

        if ($lastDate === null || $lastDate < now()->subDays(2)->toDateString()) {
            // Reset streak
            $child->update(['streak_days' => 1, 'last_activity_date' => $today]);
        } elseif ($lastDate < $today) {
            // Extend streak
            $child->increment('streak_days');
            $child->update(['last_activity_date' => $today]);
        }
        // Same day = no change
    }
}
