<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Course;
use App\Models\Activity;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with('course')->orderBy('course_id')->orderBy('order')->paginate(20);
        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        $courses = Course::all();
        $activities = Activity::all();
        return view('admin.modules.create', compact('courses', 'activities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'activities' => 'array',
            'activities.*' => 'exists:activities,id',
        ]);
        $module = Module::create($data);
        if (!empty($data['activities'])) {
            $module->activities()->sync($data['activities']);
        }
        return redirect()->route('admin.modules.index')->with('success', 'Module created.');
    }

    public function edit(Module $module)
    {
        $courses = Course::all();
        $activities = Activity::all();
        $selected = $module->activities->pluck('id')->toArray();
        return view('admin.modules.edit', compact('module', 'courses', 'activities', 'selected'));
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'activities' => 'array',
            'activities.*' => 'exists:activities,id',
        ]);
        $module->update($data);
        $module->activities()->sync($data['activities'] ?? []);
        return redirect()->route('admin.modules.index')->with('success', 'Module updated.');
    }

    public function destroy(Module $module)
    {
        $module->activities()->detach();
        $module->delete();
        return redirect()->route('admin.modules.index')->with('success', 'Module deleted.');
    }
}

