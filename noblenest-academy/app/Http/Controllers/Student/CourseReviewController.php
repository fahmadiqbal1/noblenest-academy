<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseReview;
use App\Models\TeacherCourse;
use Illuminate\Http\Request;

class CourseReviewController extends Controller
{
    /**
     * Store or update a review for a course the student is enrolled in.
     */
    public function store(Request $request, TeacherCourse $course)
    {
        $user = $request->user();

        // Verify the student is enrolled
        abort_unless(
            $user->enrolledCourses()->where('teacher_courses.id', $course->id)->exists(),
            403,
            'You must be enrolled to leave a review.'
        );

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        CourseReview::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            [...$validated, 'is_visible' => true]
        );

        return back()->with('success', 'Thank you for your review!');
    }

    /**
     * Delete the authenticated user's review for a course.
     */
    public function destroy(TeacherCourse $course)
    {
        CourseReview::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'Review removed.');
    }
}
