<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherCourse;
use App\Models\TeacherCourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    // ------------------------------------------------------------------
    // Index
    // ------------------------------------------------------------------

    public function index()
    {
        $courses = TeacherCourse::where('teacher_id', Auth::id())
                                ->withCount(['enrollments', 'classSessions'])
                                ->orderByDesc('created_at')
                                ->paginate(12);

        return view('teacher.courses.index', compact('courses'));
    }

    // ------------------------------------------------------------------
    // Create / Store
    // ------------------------------------------------------------------

    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateCourse($request);

        // File uploads
        if ($request->hasFile('thumbnail')) {
            $f = $request->file('thumbnail');
            $data['thumbnail'] = $f->storeAs('teacher-thumbnails', \Illuminate\Support\Str::uuid() . '.' . $f->getClientOriginalExtension(), 'public');
        }
        if ($request->hasFile('syllabus_file')) {
            $f = $request->file('syllabus_file');
            $data['syllabus_file'] = $f->storeAs('teacher-syllabi', \Illuminate\Support\Str::uuid() . '.' . $f->getClientOriginalExtension(), 'public');
        }

        $data['teacher_id'] = Auth::id();
        $data['slug']       = TeacherCourse::generateSlug($data['title']);

        $course = TeacherCourse::create($data);

        // Store sections if provided
        $this->syncSections($course, $request->input('sections', []));

        return redirect()->route('teacher.courses.show', $course)
                         ->with('status', 'Course created successfully!');
    }

    // ------------------------------------------------------------------
    // Show
    // ------------------------------------------------------------------

    public function show(TeacherCourse $course)
    {
        $this->authoriseCourse($course);
        $course->load(['sections', 'classSessions' => fn ($q) => $q->orderBy('starts_at'), 'enrollments.student', 'inviteLinks']);

        return view('teacher.courses.show', compact('course'));
    }

    // ------------------------------------------------------------------
    // Edit / Update
    // ------------------------------------------------------------------

    public function edit(TeacherCourse $course)
    {
        $this->authoriseCourse($course);

        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Request $request, TeacherCourse $course)
    {
        $this->authoriseCourse($course);

        $data = $this->validateCourse($request, $course->id);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $f = $request->file('thumbnail');
            $data['thumbnail'] = $f->storeAs('teacher-thumbnails', \Illuminate\Support\Str::uuid() . '.' . $f->getClientOriginalExtension(), 'public');
        }
        if ($request->hasFile('syllabus_file')) {
            if ($course->syllabus_file) {
                Storage::disk('public')->delete($course->syllabus_file);
            }
            $f = $request->file('syllabus_file');
            $data['syllabus_file'] = $f->storeAs('teacher-syllabi', \Illuminate\Support\Str::uuid() . '.' . $f->getClientOriginalExtension(), 'public');
        }

        $course->update($data);
        $this->syncSections($course, $request->input('sections', []));

        return back()->with('status', 'Course updated.');
    }

    // ------------------------------------------------------------------
    // Destroy
    // ------------------------------------------------------------------

    public function destroy(TeacherCourse $course)
    {
        $this->authoriseCourse($course);
        $course->delete();

        return redirect()->route('teacher.courses.index')->with('status', 'Course deleted.');
    }

    // ------------------------------------------------------------------
    // Publish / Unpublish toggle
    // ------------------------------------------------------------------

    public function togglePublish(TeacherCourse $course)
    {
        $this->authoriseCourse($course);
        $course->update([
            'status' => $course->status === 'published' ? 'draft' : 'published',
        ]);

        return back()->with('status', 'Course status updated.');
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function authoriseCourse(TeacherCourse $course): void
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'You do not own this course.');
        }
    }

    private function validateCourse(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'what_you_learn' => 'nullable|string',
            'subject'      => 'nullable|string|max:100',
            'age_min'      => 'nullable|integer|min:0|max:18',
            'age_max'      => 'nullable|integer|min:0|max:18|gte:age_min',
            'level'        => 'required|in:beginner,intermediate,advanced',
            'language'     => 'required|string|max:8',
            'price'        => 'required|numeric|min:0',
            'currency'     => 'required|string|max:8',
            'max_students' => 'nullable|integer|min:1',
            'thumbnail'    => 'nullable|image|max:4096',
            'syllabus_file'=> 'nullable|mimes:pdf,doc,docx|max:10240',
        ]);
    }

    private function syncSections(TeacherCourse $course, array $sections): void
    {
        // Delete removed sections then upsert the rest
        $keepIds = [];
        foreach ($sections as $index => $sec) {
            if (empty($sec['title'])) {
                continue;
            }
            if (! empty($sec['id'])) {
                $section = TeacherCourseSection::where('id', $sec['id'])
                                               ->where('teacher_course_id', $course->id)
                                               ->first();
                if ($section) {
                    $section->update(['title' => $sec['title'], 'description' => $sec['description'] ?? null, 'order' => $index]);
                    $keepIds[] = $section->id;
                    continue;
                }
            }
            $new        = TeacherCourseSection::create([
                'teacher_course_id' => $course->id,
                'title'             => $sec['title'],
                'description'       => $sec['description'] ?? null,
                'order'             => $index,
            ]);
            $keepIds[] = $new->id;
        }

        $course->sections()->whereNotIn('id', $keepIds)->delete();
    }
}
