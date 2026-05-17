<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('subject', 'like', '%'.$request->q.'%');
            });
        }
        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        $activities = $query->orderBy('age_min')->paginate(20);

        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.activities.create', ['activity' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'age_min' => 'nullable|integer|min:0|max:18',
            'age_max' => 'nullable|integer|min:0|max:18',
            'age_group' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:100',
            'activity_type' => 'required|string|max:50',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
            'duration_minutes' => 'nullable|integer|min:1|max:180',
            'language' => 'nullable|string|max:10',
            'emoji' => 'nullable|string|max:10',
            'media_url' => 'nullable|string|max:500',
            'media_file' => 'nullable|file|mimes:mp4,webm,mp3,jpg,jpeg,png,gif,pdf|max:51200',
            'thumbnail_url' => 'nullable|string|max:500',
            'instructions' => 'nullable|string',
            'materials_needed' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'is_free' => 'nullable|boolean',
            'is_rtl' => 'nullable|boolean',
            'is_muslim_only' => 'nullable|boolean',
        ]);
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $data['media_url'] = $file->storeAs('media', Str::uuid().'.'.$file->getClientOriginalExtension(), 'public');
        }
        $data['is_free'] = $request->boolean('is_free');
        $data['is_rtl'] = $request->boolean('is_rtl');
        $data['is_muslim_only'] = $request->boolean('is_muslim_only');
        // Convert newline-separated text to arrays for JSON-cast columns
        if (isset($data['materials_needed']) && $data['materials_needed'] !== '') {
            $data['materials_needed'] = array_values(array_filter(array_map('trim', explode("\n", $data['materials_needed']))));
        } else {
            $data['materials_needed'] = null;
        }
        if (isset($data['learning_objectives']) && $data['learning_objectives'] !== '') {
            $data['learning_objectives'] = array_values(array_filter(array_map('trim', explode("\n", $data['learning_objectives']))));
        } else {
            $data['learning_objectives'] = null;
        }
        Activity::create($data);

        return redirect()->route('admin.activities.index')->with('status', 'Activity added!');
    }

    public function edit(Activity $activity)
    {
        return view('admin.activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'age_min' => 'nullable|integer|min:0|max:18',
            'age_max' => 'nullable|integer|min:0|max:18',
            'age_group' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:100',
            'activity_type' => 'required|string|max:50',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
            'duration_minutes' => 'nullable|integer|min:1|max:180',
            'language' => 'nullable|string|max:10',
            'emoji' => 'nullable|string|max:10',
            'media_url' => 'nullable|string|max:500',
            'media_file' => 'nullable|file|mimes:mp4,webm,mp3,jpg,jpeg,png,gif,pdf|max:51200',
            'thumbnail_url' => 'nullable|string|max:500',
            'instructions' => 'nullable|string',
            'materials_needed' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'is_free' => 'nullable|boolean',
            'is_rtl' => 'nullable|boolean',
            'is_muslim_only' => 'nullable|boolean',
        ]);
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $data['media_url'] = $file->storeAs('media', Str::uuid().'.'.$file->getClientOriginalExtension(), 'public');
        }
        $data['is_free'] = $request->boolean('is_free');
        $data['is_rtl'] = $request->boolean('is_rtl');
        $data['is_muslim_only'] = $request->boolean('is_muslim_only');
        if (isset($data['materials_needed']) && $data['materials_needed'] !== '') {
            $data['materials_needed'] = array_values(array_filter(array_map('trim', explode("\n", $data['materials_needed']))));
        } else {
            $data['materials_needed'] = null;
        }
        if (isset($data['learning_objectives']) && $data['learning_objectives'] !== '') {
            $data['learning_objectives'] = array_values(array_filter(array_map('trim', explode("\n", $data['learning_objectives']))));
        } else {
            $data['learning_objectives'] = null;
        }
        $activity->update($data);

        return redirect()->route('admin.activities.index')->with('status', 'Activity updated!');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('admin.activities.index')->with('status', 'Activity deleted!');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $file = $request->file('file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map('trim', array_shift($rows));
        $allowed = (new Activity)->getFillable();
        foreach ($rows as $row) {
            if (count($header) !== count($row)) {
                continue;
            }
            $data = array_combine($header, $row);
            // Sanitize all CSV values and only allow known fillable fields
            $data = array_map(fn ($v) => is_string($v) ? htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8') : $v, $data);
            $data = array_intersect_key($data, array_flip($allowed));
            if (empty($data['title'])) {
                continue;
            }
            Activity::updateOrCreate(['title' => $data['title']], $data);
        }

        return redirect()->back()->with('status', 'Bulk upload complete!');
    }
}
