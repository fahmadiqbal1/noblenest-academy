<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ChildActivityProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

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

        return view('activities.index', compact('activities', 'skills', 'languages'));
    }

    public function show(Request $request, Activity $activity)
    {
        $child = null;
        if ($request->filled('child')) {
            $child = \App\Models\ChildProfile::find((int) $request->input('child'));
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
        $path = 'tracings/' . ($user ? $user->id : 'guest') . '/' . $activity->id . '_' . time() . '.png';
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
        $path = 'drawings/' . ($user ? $user->id : 'guest') . '/' . $activity->id . '_' . time() . '.png';
        Storage::disk('public')->put($path, $decoded);
        $this->recordProgress($request, $activity);
        return response()->json(['message' => 'Drawing saved!']);
    }

    public function showTracing(Activity $activity)
    {
        if ($activity->activity_type !== 'tracing') {
            abort(404);
        }
        return view('activities.tracing', compact('activity'));
    }

    public function showDrawing(Activity $activity)
    {
        if ($activity->activity_type !== 'drawing') {
            abort(404);
        }
        return view('activities.drawing', compact('activity'));
    }

    public function showPuzzle(Activity $activity)
    {
        if ($activity->activity_type !== 'puzzle') {
            abort(404);
        }
        return view('activities.puzzle', compact('activity'));
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
            $child = \App\Models\ChildProfile::find((int) $request->input('child'));
        }

        // Allow any activity with a video_url, not just those typed as 'video'.
        if (!$activity->video_url && !$activity->media_url) {
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
            $child = \App\Models\ChildProfile::find((int) $request->input('child'));
        }

        $activity->loadMissing('steps');

        // Need steps or slide_content to show anything
        if ($activity->steps->isEmpty() && !$activity->media_url) {
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
        if (!$dataUrl || !str_starts_with($dataUrl, 'data:image/png;base64,')) {
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
     * Persist activity completion to both progress tables.
     */
    private function recordProgress(Request $request, Activity $activity): void
    {
        $user = $request->user();
        if (!$user) {
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
