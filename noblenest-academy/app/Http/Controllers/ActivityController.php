<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->where('published', true);

        // Filtering
        if ($request->filled('age')) {
            $query->where('age_min', '<=', $request->age)
                ->where('age_max', '>=', $request->age);
        }
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        if ($request->filled('duration_minutes')) {
            $query->where('duration_minutes', '<=', $request->duration_minutes);
        }
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        $activities = $query->orderBy('title')->paginate(20);

        // For filter dropdowns
        $skills = Activity::select('subject')->distinct()->pluck('subject')->filter()->values();
        $languages = Activity::select('language')->distinct()->pluck('language')->filter()->values();

        // Curriculum roadmap: real DB activities grouped by age_group, max 20 per group
        $roadmap = Activity::select('id', 'title', 'age_group', 'subject')
            ->whereNotNull('age_group')
            ->orderBy('age_group')
            ->orderBy('title')
            ->get()
            ->groupBy('age_group')
            ->map(fn ($group) => $group->take(20));

        return view('activities.index', compact('activities', 'skills', 'languages', 'roadmap'));
    }

    public function show(Request $request, Activity $activity)
    {
        $child = null;
        if ($request->filled('child')) {
            $child = ChildProfile::find((int) $request->input('child'));
        }

        return view('activities.show', compact('activity', 'child'));
    }

    public function saveTrace(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string', // base64 data URL
        ]);
        $activity = Activity::findOrFail($id);
        $user = $request->user();
        $decoded = $this->decodeVerifiedPng($request->input('image'));
        if ($decoded === null) {
            return response()->json(['message' => 'Invalid image data.'], 422);
        }
        $path = 'tracings/'.($user ? $user->id : 'guest').'/'.$activity->id.'_'.time().'.png';
        Storage::disk('public')->put($path, $decoded);
        $this->recordProgress($request, $activity);

        return response()->json(['message' => 'Tracing saved!']);
    }

    public function saveDrawing(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string', // base64 data URL
        ]);
        $activity = Activity::findOrFail($id);
        $decoded = $this->decodeVerifiedPng($request->input('image'));
        if ($decoded === null) {
            return response()->json(['message' => 'Invalid image data.'], 422);
        }
        $user = $request->user();
        $path = 'drawings/'.($user ? $user->id : 'guest').'/'.$activity->id.'_'.time().'.png';
        Storage::disk('public')->put($path, $decoded);
        $this->recordProgress($request, $activity);

        return response()->json(['message' => 'Drawing saved!']);
    }

    public function showTracing(Activity $activity)
    {
        return $this->showTyped($activity, 'tracing');
    }

    public function showDrawing(Activity $activity)
    {
        return $this->showTyped($activity, 'drawing');
    }

    public function showPuzzle(Activity $activity)
    {
        return $this->showTyped($activity, 'puzzle');
    }

    private function showTyped(Activity $activity, string $type): View
    {
        if ($activity->activity_type !== $type) {
            abort(404);
        }

        return view("activities.{$type}", compact('activity'));
    }

    public function savePuzzleComplete(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $this->recordProgress($request, $activity);

        return response()->json(['message' => 'Puzzle marked as complete!']);
    }

    /**
     * Dedicated video player page for video-type activities.
     */
    public function showVideo(Request $request, Activity $activity)
    {
        $child = null;
        if ($request->filled('child')) {
            $child = ChildProfile::find((int) $request->input('child'));
        }

        // Allow any activity with a video_url, not just those typed as 'video'.
        if (! $activity->video_url && ! $activity->media_url) {
            // Fall back to the show page if no video content
            return redirect()->route('activities.show', $activity)->with('info', 'No video available for this activity.');
        }

        $activity->loadMissing('steps');

        return view('activities.video', compact('activity', 'child'));
    }

    /**
     * Dedicated slides / simulation viewer for slides-type activities.
     * Uses the step-player component in fullscreen mode.
     */
    public function showSlides(Request $request, Activity $activity)
    {
        $child = null;
        if ($request->filled('child')) {
            $child = ChildProfile::find((int) $request->input('child'));
        }

        $activity->loadMissing('steps');

        // Need steps or slide_content to show anything
        if ($activity->steps->isEmpty() && ! $activity->media_url) {
            return redirect()->route('activities.show', $activity)->with('info', 'No slides available yet — check back soon!');
        }

        return view('activities.slides', compact('activity', 'child'));
    }

    /**
     * Validate and decode a base64-encoded PNG data URL.
     * Returns the raw binary bytes, or null if the data is invalid / not a PNG.
     */
    private function decodeVerifiedPng(?string $dataUrl): ?string
    {
        if (! $dataUrl || ! str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return null;
        }
        $b64 = substr($dataUrl, strlen('data:image/png;base64,'));
        $bytes = base64_decode($b64, strict: true);
        if ($bytes === false) {
            return null;
        }
        // Verify PNG magic bytes: \x89PNG\r\n\x1a\n
        if (substr($bytes, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return null;
        }

        return $bytes;
    }

    /**
     * Persist activity completion to BOTH progress stores — this dual-write
     * is intentional, not a bug (see docs/QA_FINDINGS.md "Progress tables"):
     *
     *   - activity_user_progress  : keyed by user_id. The analytics grain
     *     (AnalyticsController monthly-completions / most-active). Covers
     *     activities opened without a child context (parent/preview).
     *   - child_activity_progress : keyed by child_profile_id. The product
     *     grain — drives the child dashboard, drip unlocks, skill states.
     *
     * They answer different questions and are deliberately not merged;
     * collapsing them would force an analytics rewrite for no user benefit.
     */
    private function recordProgress(Request $request, Activity $activity): void
    {
        $user = $request->user();
        if (! $user) {
            return;
        }
        DB::table('activity_user_progress')->updateOrInsert(
            ['user_id' => $user->id, 'activity_id' => $activity->id],
            ['completed_at' => now(), 'updated_at' => now(), 'created_at' => now()]
        );
        if ($request->filled('child')) {
            ChildActivityProgress::firstOrCreate(
                ['child_profile_id' => (int) $request->input('child'), 'activity_id' => $activity->id],
                ['completed_at' => now()]
            );
        }
    }
}
