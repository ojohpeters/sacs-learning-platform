<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;

class LearningController extends Controller
{
    /**
     * Show the course player with curriculum sidebar.
     */
    public function show(Course $course)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (! $enrollment) {
            return redirect()->route('student.courses')
                ->with('error', 'You are not enrolled in this course.');
        }

        $course->load(['sections.lessons' => function ($query) {
            $query->orderBy('order');
        }]);

        $firstLesson = $course->sections->first()?->lessons->first();

        // Get completed lesson IDs for progress
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('enrollment_id', $enrollment->id)
            ->pluck('lesson_id')
            ->toArray();

        // Calculate progress
        $totalLessons = $course->lessons()->count();
        $completedCount = count($completedLessonIds);
        $progressPercent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        // Get previous and next lessons
        $prevLesson = null;
        $nextLesson = null;
        $currentLesson = $firstLesson;

        if ($currentLesson) {
            $allLessons = $this->getOrderedLessons($course);
            $currentIndex = $allLessons->search(fn ($l) => $l->id === $currentLesson->id);
            $prevLesson = $allLessons[$currentIndex - 1] ?? null;
            $nextLesson = $allLessons[$currentIndex + 1] ?? null;
        }

        return view('learning.player', compact(
            'course',
            'enrollment',
            'currentLesson',
            'completedLessonIds',
            'progressPercent',
            'completedCount',
            'totalLessons',
            'prevLesson',
            'nextLesson'
        ));
    }

    /**
     * Show a specific lesson within the course player.
     */
    public function showLesson(Course $course, Lesson $lesson)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (! $enrollment) {
            return redirect()->route('student.courses')
                ->with('error', 'You are not enrolled in this course.');
        }

        // Verify lesson belongs to this course
        if ($lesson->section->course_id !== $course->id) {
            abort(404);
        }

        $course->load(['sections.lessons' => function ($query) {
            $query->orderBy('order');
        }]);

        // Get completed lesson IDs
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('enrollment_id', $enrollment->id)
            ->pluck('lesson_id')
            ->toArray();

        // Calculate progress
        $totalLessons = $course->lessons()->count();
        $completedCount = count($completedLessonIds);
        $progressPercent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        // Get previous and next lessons
        $allLessons = $this->getOrderedLessons($course);
        $currentIndex = $allLessons->search(fn ($l) => $l->id === $lesson->id);
        $prevLesson = $allLessons[$currentIndex - 1] ?? null;
        $nextLesson = $allLessons[$currentIndex + 1] ?? null;

        $currentLesson = $lesson;
        $isCompleted = in_array($lesson->id, $completedLessonIds);

        return view('learning.player', compact(
            'course',
            'enrollment',
            'currentLesson',
            'completedLessonIds',
            'progressPercent',
            'completedCount',
            'totalLessons',
            'prevLesson',
            'nextLesson',
            'isCompleted'
        ));
    }

    /**
     * Mark a lesson as complete or incomplete (toggle).
     */
    public function toggleComplete(Course $course, Lesson $lesson)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (! $enrollment) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        if ($lesson->section->course_id !== $course->id) {
            return response()->json(['error' => 'Invalid lesson'], 400);
        }

        // Check if already completed
        $existing = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($existing) {
            // Unmark
            $existing->delete();
            $completed = false;
        } else {
            // Mark complete
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'enrollment_id' => $enrollment->id,
                'completed_at' => now(),
            ]);
            $completed = true;
        }

        // Calculate new progress
        $totalLessons = $course->lessons()->count();
        $completedCount = LessonCompletion::where('user_id', $user->id)
            ->where('enrollment_id', $enrollment->id)
            ->count();
        $progressPercent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        return response()->json([
            'completed' => $completed,
            'progressPercent' => $progressPercent,
            'completedCount' => $completedCount,
            'totalLessons' => $totalLessons,
        ]);
    }

    /**
     * Show the live (synchronous) sessions schedule for an enrolled student.
     */
    public function sessions(Course $course)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (! $enrollment) {
            return redirect()->route('student.courses')
                ->with('error', 'You are not enrolled in this course.');
        }

        $upcoming = $course->sessions()
            ->whereDate('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $past = $course->sessions()
            ->whereDate('session_date', '<', now()->toDateString())
            ->orderByDesc('session_date')
            ->orderByDesc('start_time')
            ->get();

        return view('learning.sessions', compact('course', 'enrollment', 'upcoming', 'past'));
    }

    /**
     * Verify user is enrolled in the course.
     */
    private function verifyEnrollment($user, Course $course): ?Enrollment
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Get all lessons in order across all sections.
     */
    private function getOrderedLessons(Course $course)
    {
        return $course->lessons()->orderBy('sections.order')->orderBy('lessons.order')->get();
    }
}
