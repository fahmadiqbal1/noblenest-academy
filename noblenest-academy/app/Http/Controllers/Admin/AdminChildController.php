<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use Illuminate\Http\Request;

class AdminChildController extends Controller
{
    public function index(Request $request)
    {
        $query = ChildProfile::with('parent');

        if ($request->filled('language')) {
            $query->where('preferred_language', $request->language);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->q.'%')
                    ->orWhere('nickname', 'like', '%'.$request->q.'%');
            });
        }

        $children = $query->latest()->paginate(30);
        $languages = ChildProfile::distinct()->pluck('preferred_language')->sort()->values();

        return view('admin.children.index', compact('children', 'languages'));
    }
}
