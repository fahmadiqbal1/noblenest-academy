<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();
        // Advanced filtering
        if ($request->filled('age')) {
            $query->where('age_min', '<=', $request->age)
                  ->where('age_max', '>=', $request->age);
        }
        if ($request->filled('skill')) {
            $query->where('skill', $request->skill);
        }
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }
        // Search by title or description
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%'.$request->q.'%')
                  ->orWhere('description', 'like', '%'.$request->q.'%');
            });
        }
        // Sorting
        $sort = $request->get('sort', 'age_min');
        $dir = $request->get('dir', 'asc');
        $activities = $query->orderBy($sort, $dir)->paginate(18);
        return view('admin.curriculum', compact('activities'));
    }

    // Bulk import/export for curriculum management
    public function import(Request $request)
    {
        // TODO: Implement CSV/Excel import logic
        return back()->with('info', 'Import feature coming soon!');
    }
    public function export(Request $request)
    {
        // TODO: Implement CSV/Excel export logic
        return back()->with('info', 'Export feature coming soon!');
    }
}
