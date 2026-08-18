<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;

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
        }, 'sections.quiz']);

        // Get completed lesson IDs
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('enrollment_id', $enrollment->id)
            ->pluck('lesson_id')
            ->toArray();

        // Get passed quiz IDs
        $passedQuizIds = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'passed')
            ->pluck('quiz_id')
            ->toArray();

        // Calculate progress
        $progressData = $this->calculateProgress($course, $user, $enrollment, $completedLessonIds);
        $progressPercent = $progressData['percent'];
        $completedCount = $progressData['completed'];
        $totalCount = $progressData['total'];

        // Get section access
        $unlockedSections = $this->getUnlockedSections($course, $completedLessonIds, $passedQuizIds);
        $accessibleLessonIds = $this->getAccessibleLessonIds($course, $completedLessonIds, $unlockedSections);

        // If no lessons completed yet, show the welcome screen (fresh enrollment)
        if (count($completedLessonIds) === 0) {
            return view('learning.player', compact(
                'course',
                'enrollment',
                'completedLessonIds',
                'passedQuizIds',
                'progressPercent',
                'completedCount',
                'totalCount',
                'unlockedSections',
                'accessibleLessonIds'
            ));
        }

        // Find the current lesson — first accessible lesson not yet completed
        $currentLesson = null;

        foreach ($this->getOrderedLessons($course) as $lesson) {
            if (!in_array($lesson->id, $accessibleLessonIds)) {
                continue;
            }

            if (!in_array($lesson->id, $completedLessonIds)) {
                $currentLesson = $lesson;
                break;
            }
        }

        // If all lessons are complete, show the last lesson
        if (!$currentLesson) {
            $currentLesson = $this->getOrderedLessons($course)->last();
        }

        // If there's a current lesson, redirect to it
        if ($currentLesson) {
            return redirect()->route('learning.lesson', [$course->slug, $currentLesson->id]);
        }

        // Fallback — show welcome screen (no lessons exist)
        return view('learning.player', compact(
            'course',
            'enrollment',
            'completedLessonIds',
            'passedQuizIds',
            'progressPercent',
            'completedCount',
            'totalCount',
            'unlockedSections',
            'accessibleLessonIds'
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
        }, 'sections.quiz']);

        // Get completed lesson IDs
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('enrollment_id', $enrollment->id)
            ->pluck('lesson_id')
            ->toArray();

        // Get passed quiz IDs
        $passedQuizIds = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'passed')
            ->pluck('quiz_id')
            ->toArray();

        // Check if this lesson's section is locked (previous section quiz not passed)
        $unlockedSections = $this->getUnlockedSections($course, $completedLessonIds, $passedQuizIds);
        $accessibleLessonIds = $this->getAccessibleLessonIds($course, $completedLessonIds, $unlockedSections);

        if (! in_array($lesson->id, $accessibleLessonIds)) {
            $lastAccessibleId = end($accessibleLessonIds);
            $lastLesson = Lesson::find($lastAccessibleId);
            if ($lastLesson) {
                return redirect()->route('learning.lesson', [$course->slug, $lastLesson->id])
                    ->with('error', 'Complete the previous lesson before continuing.');
            }
            return redirect()->route('learning.course', $course->slug)
                ->with('error', 'Complete the previous lesson before continuing.');
        }

        // Calculate progress
        $progressData = $this->calculateProgress($course, $user, $enrollment, $completedLessonIds);
        $progressPercent = $progressData['percent'];
        $completedCount = $progressData['completed'];
        $totalCount = $progressData['total'];

        // Get previous and next lessons
        $nextItem = $this->getNextItem($course, $lesson, $completedLessonIds, $passedQuizIds);
        $prevItem = $this->getPrevItem($course, $lesson, $completedLessonIds, $passedQuizIds);

        $currentLesson = $lesson;
        $isCompleted = in_array($lesson->id, $completedLessonIds);

        // Start the time-on-lesson clock and expose the requirement to the view.
        [$requiredSeconds, $secondsSpent] = $this->startLessonClock($user, $enrollment, $course, $lesson);
        $heartbeatInterval = (int) config('learning.heartbeat_interval', 15);

        return view('learning.player', compact(
            'course',
            'enrollment',
            'currentLesson',
            'completedLessonIds',
            'passedQuizIds',
            'progressPercent',
            'completedCount',
            'totalCount',
            'prevItem',
            'nextItem',
            'isCompleted',
            'unlockedSections',
            'accessibleLessonIds',
            'requiredSeconds',
            'secondsSpent',
            'heartbeatInterval'
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
            $existing->delete();
            $completed = false;
        } else {
            // Gate: the student must have accumulated enough active time.
            $required = $this->requiredSecondsFor($course, $lesson);
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

            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'enrollment_id' => $enrollment->id,
                'completed_at' => now(),
            ]);
            $completed = true;
        }

        // Calculate new progress
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('enrollment_id', $enrollment->id)
            ->pluck('lesson_id')
            ->toArray();

        $progressData = $this->calculateProgress($course, $user, $enrollment, $completedLessonIds);

        return response()->json([
            'completed' => $completed,
            'progressPercent' => $progressData['percent'],
            'completedCount' => $progressData['completed'],
            'totalCount' => $progressData['total'],
        ]);
    }

    private function getAccessibleLessonIds(Course $course, array $completedLessonIds, array $unlockedSections): array
    {
        $accessible = [];
        $canProceed = true;

        foreach ($this->getOrderedLessons($course) as $lesson) {
            if (! in_array($lesson->section_id, $unlockedSections)) {
                $canProceed = false;

                continue;
            }

            if (! $canProceed) {
                continue;
            }

            $accessible[] = $lesson->id;

            if (! in_array($lesson->id, $completedLessonIds)) {
                $canProceed = false;
            }
        }

        return $accessible;
    }

    /**
     * Record active time on a lesson (heartbeat). Credited time is clamped to
     * the real interval elapsed since the last ping, so a client can't inflate
     * its progress by sending heartbeats faster than real time.
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
            // On Carbon 3, $last->diffInSeconds($now) is the positive elapsed time.
            $elapsed = $progress->last_heartbeat_at
                ? (int) $progress->last_heartbeat_at->diffInSeconds($now)
                : $interval;
            $credit = max(0, min($elapsed, $interval + 5));
        }

        $progress->seconds_spent += $credit;
        $progress->last_heartbeat_at = $now;
        $progress->save();

        $required = $this->requiredSecondsFor($course, $lesson);

        return response()->json([
            'secondsSpent' => $progress->seconds_spent,
            'requiredSeconds' => $required,
            'canComplete' => $progress->seconds_spent >= $required,
        ]);
    }

    /**
     * Ensure a progress row exists for this lesson (starting its clock on the
     * first open) and return [requiredSeconds, secondsSpent] for the view.
     *
     * @return array{0:int,1:int}
     */
    private function startLessonClock($user, Enrollment $enrollment, Course $course, Lesson $lesson): array
    {
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['enrollment_id' => $enrollment->id, 'started_at' => now(), 'seconds_spent' => 0]
        );

        return [$this->requiredSecondsFor($course, $lesson), (int) $progress->seconds_spent];
    }

    /**
     * Effective required active time: our content-aware per-lesson requirement,
     * with the course's per-course minimum (lesson_min_minutes) applied as a floor.
     */
    private function requiredSecondsFor(Course $course, Lesson $lesson): int
    {
        // An explicit per-lesson override always wins (0 = no gate).
        if ($lesson->min_seconds !== null) {
            return max(0, (int) $lesson->min_seconds);
        }

        // Otherwise use our content-aware requirement, with the course's
        // per-course minimum (lesson_min_minutes) applied as a floor.
        $courseFloor = (int) ($course->lesson_min_minutes ?? 0) * 60;

        return max($lesson->requiredSeconds(), $courseFloor);
    }

    /**
     * Show the live (synchronous) sessions schedule.
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
     * Calculate overall progress including lessons and quizzes.
     */
    private function calculateProgress(Course $course, $user, $enrollment, $completedLessonIds): array
    {
        $totalLessons = $course->lessons()->count();
        $completedLessons = count($completedLessonIds);

        // Count quizzes
        $totalQuizzes = Quiz::whereIn('section_id', $course->sections()->pluck('id'))
            ->where('is_active', true)
            ->count();

        $passedQuizzes = QuizAttempt::where('user_id', $user->id)
            ->whereIn('quiz_id', Quiz::whereIn('section_id', $course->sections()->pluck('id'))->pluck('id'))
            ->where('status', 'passed')
            ->count();

        $totalItems = $totalLessons + $totalQuizzes;
        $completedItems = $completedLessons + $passedQuizzes;
        $percent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        return [
            'percent' => $percent,
            'completed' => $completedItems,
            'total' => $totalItems,
        ];
    }

    /**
     * Determine which sections are unlocked for the student.
     * A section is locked if the previous section has a quiz that hasn't been passed.
     */
    private function getUnlockedSections(Course $course, array $completedLessonIds, array $passedQuizIds): array
    {
        $sections = $course->sections()->orderBy('order')->get();
        $unlocked = [];
        $previousQuizPassed = true; // First section is always unlocked

        foreach ($sections as $section) {
            if ($previousQuizPassed) {
                $unlocked[] = $section->id;
            }

            // Check if this section has a quiz and if it's been passed
            if ($section->quiz && $section->quiz->is_active) {
                $previousQuizPassed = in_array($section->quiz->id, $passedQuizIds);
            } else {
                $previousQuizPassed = true;
            }
        }

        return $unlocked;
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

    /**
     * Get the quiz for a section, if it exists and is active.
     */
    private function getSectionQuiz($section)
    {
        if ($section->quiz && $section->quiz->is_active) {
            return $section->quiz;
        }
        return null;
    }

    /**
     * Get the next item after a lesson — could be next lesson, or quiz, or next section lesson.
     */
    private function getNextItem(Course $course, Lesson $currentLesson, array $completedLessonIds, array $passedQuizIds)
    {
        $sections = $course->sections()->orderBy('order')->get();
        $currentSection = $currentLesson->section;

        // Get all lessons in current section
        $sectionLessons = $currentSection->lessons()->orderBy('order')->get();
        $currentIndex = $sectionLessons->search(fn($l) => $l->id === $currentLesson->id);

        // Is there a next lesson in this section?
        if ($currentIndex < $sectionLessons->count() - 1) {
            return [
                'type' => 'lesson',
                'lesson' => $sectionLessons[$currentIndex + 1],
            ];
        }

        // End of section — check for quiz
        $quiz = $this->getSectionQuiz($currentSection);
        if ($quiz && !in_array($quiz->id, $passedQuizIds)) {
            return [
                'type' => 'quiz',
                'quiz' => $quiz,
            ];
        }

        // Quiz passed or no quiz — go to next section
        $currentSectionIndex = $sections->search(fn($s) => $s->id === $currentSection->id);
        if ($currentSectionIndex < $sections->count() - 1) {
            $nextSection = $sections[$currentSectionIndex + 1];
            $firstLesson = $nextSection->lessons()->orderBy('order')->first();
            if ($firstLesson) {
                return [
                    'type' => 'lesson',
                    'lesson' => $firstLesson,
                ];
            }
        }

        // End of course
        return null;
    }

    /**
     * Get the previous item before a lesson.
     */
    private function getPrevItem(Course $course, Lesson $currentLesson, array $completedLessonIds, array $passedQuizIds)
    {
        $sections = $course->sections()->orderBy('order')->get();
        $currentSection = $currentLesson->section;

        $sectionLessons = $currentSection->lessons()->orderBy('order')->get();
        $currentIndex = $sectionLessons->search(fn($l) => $l->id === $currentLesson->id);

        // Is there a previous lesson in this section?
        if ($currentIndex > 0) {
            return [
                'type' => 'lesson',
                'lesson' => $sectionLessons[$currentIndex - 1],
            ];
        }

        // First lesson in section — check previous section
        $currentSectionIndex = $sections->search(fn($s) => $s->id === $currentSection->id);
        if ($currentSectionIndex > 0) {
            $prevSection = $sections[$currentSectionIndex - 1];

            // Check if previous section has a quiz
            $quiz = $this->getSectionQuiz($prevSection);
            if ($quiz && !in_array($quiz->id, $passedQuizIds)) {
                return [
                    'type' => 'quiz',
                    'quiz' => $quiz,
                ];
            }

            // Go to last lesson of previous section
            $lastLesson = $prevSection->lessons()->orderByDesc('order')->first();
            if ($lastLesson) {
                return [
                    'type' => 'lesson',
                    'lesson' => $lastLesson,
                ];
            }
        }

        return null;
    }
}
