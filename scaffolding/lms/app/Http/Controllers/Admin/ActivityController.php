<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%')
                  ->orWhere('skill', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $activities = $query->orderBy('age_min')->paginate(20);
        return view('admin.activities.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'age_min' => 'nullable|integer',
            'age_max' => 'nullable|integer',
            'skill' => 'nullable|string',
            'type' => 'required',
            'language' => 'nullable|string',
            'media_url' => 'nullable|string',
            'media_file' => 'nullable|file',
        ]);
        if ($request->hasFile('media_file')) {
            $data['media_url'] = $request->file('media_file')->store('media', 'public');
        }
        Activity::create($data);
        return redirect()->back()->with('success', 'Activity added!');
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'age_min' => 'nullable|integer',
            'age_max' => 'nullable|integer',
            'skill' => 'nullable|string',
            'type' => 'required',
            'language' => 'nullable|string',
            'media_url' => 'nullable|string',
            'media_file' => 'nullable|file',
        ]);
        if ($request->hasFile('media_file')) {
            $data['media_url'] = $request->file('media_file')->store('media', 'public');
        }
        $activity->update($data);
        return redirect()->back()->with('success', 'Activity updated!');
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        return redirect()->back()->with('success', 'Activity deleted!');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');
        $rows = array_map('str_getcsv', file($file));
        $header = array_map('trim', array_shift($rows));
        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            Activity::updateOrCreate(['title' => $data['title']], $data);
        }
        return redirect()->back()->with('success', 'Bulk upload complete!');
    }
}

