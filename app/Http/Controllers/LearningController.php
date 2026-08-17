<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

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

        // Start/resume the timer only for this lesson while the student is viewing it
        $timerState = $this->getLessonTimerState($course, $enrollment, $lesson, activate: true);
        $canMarkComplete = $timerState['canMarkComplete'];
        $remainingSeconds = $timerState['remainingSeconds'];

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
            'canMarkComplete',
            'remainingSeconds'
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
            if (! $this->canMarkLessonComplete($course, $enrollment, $lesson)) {
                $remainingSeconds = $this->getLessonTimerState($course, $enrollment, $lesson)['remainingSeconds'];
                $minMinutes = max(1, (int) ($course->lesson_min_minutes ?? 1));
                $remainingMinutes = max(1, (int) ceil($remainingSeconds / 60));

                return response()->json([
                    'error' => "Please spend at least {$minMinutes} minute(s) on this lesson. About {$remainingMinutes} minute(s) remaining.",
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

    private function lessonTimeSessionKey(int $enrollmentId, int $lessonId, string $suffix): string
    {
        return "lesson_{$suffix}_{$enrollmentId}_{$lessonId}";
    }

    private function flushActiveLessonTime(int $enrollmentId): void
    {
        $activeLessonId = session("active_lesson_{$enrollmentId}");
        if (!$activeLessonId) {
            return;
        }

        $segmentKey = $this->lessonTimeSessionKey($enrollmentId, $activeLessonId, 'segment_start');
        $accumulatedKey = $this->lessonTimeSessionKey($enrollmentId, $activeLessonId, 'accumulated');
        $segmentStart = session($segmentKey);

        if ($segmentStart && $this->isValidLessonStartTime($segmentStart)) {
            $elapsed = (int) \Carbon\Carbon::parse($segmentStart)->diffInSeconds(now());
            session([$accumulatedKey => (int) session($accumulatedKey, 0) + $elapsed]);
        }

        session()->forget($segmentKey);
        session()->forget("active_lesson_{$enrollmentId}");
    }

    private function activateLessonTimer(Enrollment $enrollment, Lesson $lesson): void
    {
        $enrollmentId = $enrollment->id;
        $lessonId = $lesson->id;
        $activeLessonId = session("active_lesson_{$enrollmentId}");
        $segmentKey = $this->lessonTimeSessionKey($enrollmentId, $lessonId, 'segment_start');

        // Same lesson already active — preserve the timer
        if ($activeLessonId && (int) $activeLessonId === $lessonId) {
            $segmentStart = session($segmentKey);

            if (!$segmentStart || !$this->isValidLessonStartTime($segmentStart)) {
                session([$segmentKey => now()->toIso8601String()]);
            }

            return;
        }

        // Different lesson — flush previous time
        if ($activeLessonId) {
            $this->flushActiveLessonTime($enrollmentId);
        }

        // Start new timer
        session(["active_lesson_{$enrollmentId}" => $lessonId]);
        session([$segmentKey => now()->toIso8601String()]);
    }

    private function getLessonTimeSpentSeconds(int $enrollmentId, int $lessonId): int
    {
        $total = (int) session($this->lessonTimeSessionKey($enrollmentId, $lessonId, 'accumulated'), 0);
        $activeLessonId = session("active_lesson_{$enrollmentId}");

        if ((int) $activeLessonId !== $lessonId) {
            return $total;
        }

        $segmentStart = session($this->lessonTimeSessionKey($enrollmentId, $lessonId, 'segment_start'));
        if ($segmentStart && $this->isValidLessonStartTime($segmentStart)) {
            $total += (int) \Carbon\Carbon::parse($segmentStart)->diffInSeconds(now());
        }

        return $total;
    }

    private function getLessonTimerState(Course $course, Enrollment $enrollment, Lesson $lesson, bool $activate = false): array
    {
        $minMinutes = max(1, (int) ($course->lesson_min_minutes ?? 1));
        $minSeconds = $minMinutes * 60;

        if ($activate) {
            $this->activateLessonTimer($enrollment, $lesson);
        }

        $spentSeconds = $this->getLessonTimeSpentSeconds($enrollment->id, $lesson->id);
        $remainingSeconds = max(0, $minSeconds - $spentSeconds);

        return [
            'canMarkComplete' => $remainingSeconds <= 0,
            'remainingSeconds' => $remainingSeconds,
            'timeSpentSeconds' => $spentSeconds,
        ];
    }

    private function canMarkLessonComplete(Course $course, Enrollment $enrollment, Lesson $lesson): bool
    {
        return $this->getLessonTimerState($course, $enrollment, $lesson)['canMarkComplete'];
    }

    private function isValidLessonStartTime($value): bool
    {
        if (! $value) {
            return false;
        }

        try {
            $date = \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return false;
        }

        return $date->lte(now()->addMinute());
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
