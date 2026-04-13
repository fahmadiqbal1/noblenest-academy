<?php

namespace App\Http\Controllers;

use App\Events\ActivityCompleted;
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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Active subscription lookup
        $subscription = $user->subscriptions()
            ->where('active', true)
            ->where('ends_at', '>', now())
            ->first();

        $hasSubscription = $subscription !== null;

        // Base query: age + language appropriate activities with their module pivot
        $ageYears = $child->age_months ? ($child->age_months / 12) : null;
        $lang = $child->preferred_language ?: 'en';
        $query = Activity::query()
            ->with('modules')
            ->where(function ($q) use ($lang) {
                $q->where('language', $lang)->orWhereNull('language');
            });
        if ($ageYears !== null) {
            $query->where('age_min', '<=', $ageYears)
                  ->where('age_max', '>=', $ageYears);
        }

        // Gate Quran & Islamic-studies & Arabic activities to Muslim children only.
        if (!$child->is_muslim) {
            $query->whereNotIn('subject', ['quran', 'islamic_studies', 'arabic']);
        }

        // Optional subject filter
        $activeSubject = null;
        if ($request->filled('subject')) {
            $activeSubject = $request->input('subject');
            $subjectMap = [
                'islamic'  => ['quran', 'arabic', 'islamic_studies'],
                'stories'  => ['literacy', 'stories'],
                'language' => ['language'],
                'art'      => ['art'],
                'motor'    => ['motor'],
                'stem'     => ['stem', 'science', 'numeracy', 'coding'],
            ];
            $dbSubjects = $subjectMap[$activeSubject] ?? [$activeSubject];
            $query->whereIn('subject', $dbSubjects);
        }

        // Drip week and order calculations
        $currentWeek    = $hasSubscription ? $subscription->currentWeek() : 0;
        $maxOrder       = $hasSubscription ? $subscription->maxActivityOrder() : 0;
        $totalWeeks     = 4;
        $daysToNextWeek = 0;

        if ($hasSubscription && $currentWeek < $totalWeeks) {
            $nextWeekStart = $subscription->starts_at->copy()->addDays($currentWeek * 7);
            $daysToNextWeek = max(0, (int) now()->diffInDays($nextWeekStart, false));
        }

        // Completed activity IDs for this child
        $completedIds = ChildActivityProgress::where('child_profile_id', $child->id)
            ->pluck('activity_id')
            ->toArray();

        // Free tier: first 7 per module (order 1-7), or is_free flag
        // Paid tier: up to maxActivityOrder per drip week
        $freeMaxOrder = 7;

        $activities = $query->paginate(18)->through(function ($activity) use ($hasSubscription, $maxOrder, $freeMaxOrder, $completedIds) {
            // Use eager-loaded modules instead of querying per activity.
            // null means the activity is not in any module — always accessible.
            $pivotOrder = $activity->modules->first()?->pivot?->order;
            $activity->pivot_order = $pivotOrder;

            if ($hasSubscription) {
                $activity->locked = $pivotOrder !== null && $pivotOrder > $maxOrder;
                $activity->unlock_week = $pivotOrder ? (int) ceil($pivotOrder / 5) : null;
            } else {
                // Free: activities not in any module are always free;
                // module activities past position 7 require premium.
                $activity->locked = $pivotOrder !== null && !$activity->is_free && $pivotOrder > $freeMaxOrder;
            }

            $activity->is_completed = in_array($activity->id, $completedIds);
            return $activity;
        });

        $completedCount = count($completedIds);
        $nextActivity   = $activities->first(fn ($a) => !($a->locked ?? false) && !($a->is_completed ?? false));

        return view('child.activities', compact(
            'child', 'activities', 'hasSubscription',
            'currentWeek', 'totalWeeks', 'maxOrder', 'daysToNextWeek', 'completedCount',
            'activeSubject', 'nextActivity'
        ));
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

            // Dispatch ActivityCompleted event to trigger skill state update and learning path recomputation
            // Calculate mastery score from activity progress
            $masteryScore = $this->calculateMasteryScore($progress);
            ActivityCompleted::dispatch($child, $activity, $progress, $masteryScore);
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

    /**
     * Calculate mastery score (0.0 - 1.0) from a ChildActivityProgress record.
     * Used by ActivityCompleted event to populate ChildSkillState.
     */
    private function calculateMasteryScore(ChildActivityProgress $progress): float
    {
        // If score is recorded, use it directly
        if ($progress->score !== null && $progress->score >= 0) {
            return min($progress->score / 100, 1.0); // Normalize to [0.0, 1.0]
        }

        // Fallback: completed_at is set = successful completion
        return $progress->completed_at ? 0.9 : 0.5; // 0.9 for successful, 0.5 for attempted but not completed
    }
}
