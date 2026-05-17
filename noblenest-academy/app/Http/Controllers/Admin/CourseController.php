<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::orderByDesc('created_at')->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load(['modules' => function ($q) {
            $q->orderBy('order')->with(['activities' => function ($aq) {
                $aq->orderByPivot('order');
            }, 'lessons' => function ($lq) {
                $lq->orderBy('order');
            }]);
        }]);

        return view('admin.courses.show', compact('course'));
    }

    public function create()
    {
        $course = new Course;

        return view('admin.courses.create', compact('course'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'age_min' => 'nullable|integer|min:0|max:18',
            'age_max' => 'nullable|integer|min:0|max:18|gte:age_min',
            'color' => 'nullable|string|max:20',
            'emoji' => 'nullable|string|max:10',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Ensure unique slug (simple approach)
        $baseSlug = $data['slug'];
        $count = 0;
        while (Course::where('slug', $data['slug'])->exists()) {
            $count++;
            $data['slug'] = $baseSlug.'-'.$count;
        }

        $course = Course::create($data);

        return redirect()->route('admin.courses.edit', $course)->with('status', 'Course created');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug,'.$course->id,
            'description' => 'nullable|string',
            'age_min' => 'nullable|integer|min:0|max:18',
            'age_max' => 'nullable|integer|min:0|max:18|gte:age_min',
            'color' => 'nullable|string|max:20',
            'emoji' => 'nullable|string|max:10',
        ]);

        $course->update($data);

        return back()->with('status', 'Course updated');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted');
    }
}
