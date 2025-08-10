<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

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
        if ($request->filled('skill')) {
            $query->where('skill', $request->skill);
        }
        if ($request->filled('duration')) {
            $query->where('duration', '<=', $request->duration);
        }
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        $activities = $query->orderBy('title')->paginate(20);

        // For filter dropdowns
        $skills = Activity::select('skill')->distinct()->pluck('skill')->filter()->values();
        $languages = Activity::select('language')->distinct()->pluck('language')->filter()->values();

        return view('activities.index', compact('activities', 'skills', 'languages'));
    }

    public function saveTrace(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string', // base64 data URL
        ]);
        $activity = Activity::findOrFail($id);
        $user = $request->user();
        // Save image to storage/app/public/tracings/{user_id}/{activity_id}_timestamp.png
        $imageData = $request->input('image');
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = $activity->id . '_' . time() . '.png';
        $path = 'tracings/' . ($user ? $user->id : 'guest') . '/' . $imageName;
        \Storage::disk('public')->put($path, base64_decode($image));
        // Mark progress as complete
        if ($user) {
            \DB::table('activity_user_progress')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'completed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
        return response()->json(['message' => 'Tracing saved!']);
    }

    public function saveDrawing(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string', // base64 data URL
        ]);
        $activity = Activity::findOrFail($id);
        $user = $request->user();
        $imageData = $request->input('image');
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = $activity->id . '_' . time() . '.png';
        $path = 'drawings/' . ($user ? $user->id : 'guest') . '/' . $imageName;
        \Storage::disk('public')->put($path, base64_decode($image));
        // Mark progress as complete
        if ($user) {
            \DB::table('activity_user_progress')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'completed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
        return response()->json(['message' => 'Drawing saved!']);
    }

    public function showTracing(Activity $activity)
    {
        if ($activity->activity_type !== 'tracing') {
            abort(404);
        }
        return view('activities.tracing', compact('activity'));
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
        $user = $request->user();
        if ($user) {
            \DB::table('activity_user_progress')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'completed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
        return response()->json(['message' => 'Puzzle marked as complete!']);
    }
}
