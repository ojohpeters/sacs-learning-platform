<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\LessonProgress;

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

        $requiredSeconds = 0;
        $secondsSpent = 0;

        if ($currentLesson) {
            $allLessons = $this->getOrderedLessons($course);
            $currentIndex = $allLessons->search(fn ($l) => $l->id === $currentLesson->id);
            $prevLesson = $allLessons[$currentIndex - 1] ?? null;
            $nextLesson = $allLessons[$currentIndex + 1] ?? null;

            [$requiredSeconds, $secondsSpent] = $this->startLessonClock($user, $enrollment, $currentLesson);
        }

        $heartbeatInterval = (int) config('learning.heartbeat_interval', 15);

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
            'requiredSeconds',
            'secondsSpent',
            'heartbeatInterval'
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

        [$requiredSeconds, $secondsSpent] = $this->startLessonClock($user, $enrollment, $lesson);
        $heartbeatInterval = (int) config('learning.heartbeat_interval', 15);

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
            'isCompleted',
            'requiredSeconds',
            'secondsSpent',
            'heartbeatInterval'
        ));
    }

    /**
     * Record active time on a lesson (heartbeat). Time credited is clamped to
     * the real interval elapsed since the last heartbeat, so a client cannot
     * inflate its progress by sending heartbeats faster than real time.
     */
    public function heartbeat(Course $course, Lesson $lesson)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (! $enrollment) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        if ($lesson->section->course_id !== $course->id) {
            return response()->json(['error' => 'Invalid lesson'], 400);
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $interval = (int) config('learning.heartbeat_interval', 15);
        $now = now();

        if (! $progress->exists) {
            $progress->enrollment_id = $enrollment->id;
            $progress->started_at = $now;
            $progress->seconds_spent = 0;
            $credit = $interval;
        } else {
            // Credit only the real time elapsed since the last ping, capped at
            // one interval (+ a little slack for jitter). Idle/backgrounded
            // gaps and rapid-fire pings therefore can't over-credit.
            $elapsed = $progress->last_heartbeat_at
                ? $now->diffInSeconds($progress->last_heartbeat_at)
                : $interval;
            $credit = max(0, min($elapsed, $interval + 5));
        }

        $progress->seconds_spent += $credit;
        $progress->last_heartbeat_at = $now;
        $progress->save();

        $required = $lesson->requiredSeconds();

        return response()->json([
            'secondsSpent' => $progress->seconds_spent,
            'requiredSeconds' => $required,
            'canComplete' => $progress->seconds_spent >= $required,
        ]);
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
            // Gate: the student must have spent enough active time on the lesson.
            $required = $lesson->requiredSeconds();
            $spent = (int) LessonProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->value('seconds_spent');

            if ($spent < $required) {
                return response()->json([
                    'error' => 'Please spend more time on this lesson before marking it complete.',
                    'secondsSpent' => $spent,
                    'requiredSeconds' => $required,
                    'secondsRemaining' => $required - $spent,
                ], 422);
            }

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
     * Ensure a progress row exists for this lesson (starting its clock on the
     * first open) and return [requiredSeconds, secondsSpent] for the view.
     *
     * @return array{0:int,1:int}
     */
    private function startLessonClock($user, Enrollment $enrollment, Lesson $lesson): array
    {
        $progress = LessonProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'enrollment_id' => $enrollment->id,
                'started_at' => now(),
                'seconds_spent' => 0,
            ]
        );

        return [$lesson->requiredSeconds(), (int) $progress->seconds_spent];
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
