<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\Crypt;

class CourseCatalogController extends Controller
{
    /**
     * Show all published courses for browsing.
     */
    public function index()
    {
        $courses = Course::where('is_published', true)
            ->withCount('enrollments')
            ->latest()
            ->paginate(12);

        return view('courses.catalog', compact('courses'));
    }

    /**
     * Show a single course detail page.
     */
    public function show(Course $course)
{
    if (!$course->is_published) {
        abort(404);
    }

    $course->load(['sections.lessons' => function ($query) {
        $query->where('is_free_preview', true);
    }]);

    // Generate checkout tokens for each learning type
    $basePayload = [
        'course_id'   => $course->id,
        'course_slug' => $course->slug,
        'timestamp'   => now()->timestamp,
    ];

    $tokens = [
        'inclass' => Crypt::encrypt(array_merge($basePayload, ['type' => 'inclass'])),
        'sync'    => Crypt::encrypt(array_merge($basePayload, ['type' => 'sync'])),
        'async'   => Crypt::encrypt(array_merge($basePayload, ['type' => 'async'])),
    ];

    return view('courses.show', compact('course', 'tokens'));
}

        /**
     * Show a free preview lesson without requiring enrollment.
     */
    public function preview(Course $course, Lesson $lesson)
    {
        // Only allow free preview lessons
        if (!$lesson->is_free_preview) {
            abort(403, 'This lesson requires enrollment.');
        }

        // Verify the lesson belongs to this course
        if ($lesson->section->course_id !== $course->id) {
            abort(404);
        }

        // Load course with sections and lessons for sidebar
        $course->load(['sections.lessons' => function ($query) {
            $query->orderBy('order');
        }]);

        $basePayload = [
            'course_id'   => $course->id,
            'course_slug' => $course->slug,
            'timestamp'   => now()->timestamp,
        ];

        $tokens = [
            'inclass' => Crypt::encrypt(array_merge($basePayload, ['type' => 'inclass'])),
            'sync'    => Crypt::encrypt(array_merge($basePayload, ['type' => 'sync'])),
            'async'   => Crypt::encrypt(array_merge($basePayload, ['type' => 'async'])),
        ];

        return view('courses.preview', compact('course', 'lesson', 'tokens'));
    }
}