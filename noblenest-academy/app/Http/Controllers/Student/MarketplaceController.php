<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TeacherCourse;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Public marketplace — browse teacher-published courses.
     * No authentication required.
     */
    public function index(Request $request)
    {
        $query = TeacherCourse::where('status', 'published')
                              ->with('teacher')
                              ->withCount('activeEnrollments');

        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        if ($request->filled('age')) {
            $query->where('age_min', '<=', $request->age)
                  ->where('age_max', '>=', $request->age);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }
        if ($request->filled('free')) {
            $query->where('price', 0);
        }

        $courses = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        $subjects = TeacherCourse::where('status', 'published')
                                  ->distinct()
                                  ->pluck('subject')
                                  ->filter()
                                  ->sort()
                                  ->values();

        return view('student.marketplace', compact('courses', 'subjects'));
    }

    /**
     * Show a single course detail (public).
     */
    public function show(TeacherCourse $course)
    {
        if (! $course->isPublished()) {
            abort(404);
        }

        $course->load(['teacher', 'sections', 'classSessions' => fn ($q) => $q->where('status', 'scheduled')->orderBy('starts_at')]);

        $enrollment = auth()->check()
            ? $course->enrollments()->where('student_id', auth()->id())->first()
            : null;

        return view('student.course_detail', compact('course', 'enrollment'));
    }
}
