<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function create()
    {
        return view('admin.activity_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'age_min' => 'required|integer|min:0|max:10',
            'age_max' => 'required|integer|min:0|max:10|gte:age_min',
            'skill' => 'required|string',
            'language' => 'required|string',
        ]);
        Activity::create($validated);
        return redirect()->route('admin.curriculum')->with('success', __('Activity created successfully!'));
    }

    public function edit(Activity $activity)
    {
        return view('admin.activity_edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'age_min' => 'required|integer|min:0|max:10',
            'age_max' => 'required|integer|min:0|max:10|gte:age_min',
            'skill' => 'required|string',
            'language' => 'required|string',
        ]);
        $activity->update($validated);
        return redirect()->route('admin.curriculum')->with('success', __('Activity updated successfully!'));
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('admin.curriculum')->with('success', __('Activity deleted successfully!'));
    }
}
