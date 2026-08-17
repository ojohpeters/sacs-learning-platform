<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Show the quiz page — start or resume an attempt.
     */
    public function show(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (!$enrollment) {
            return redirect()->route('student.courses')
                ->with('error', 'You are not enrolled in this course.');
        }

        // Verify quiz belongs to this course
        if ($quiz->section->course_id !== $course->id) {
            abort(404);
        }

        // Check if already passed
        if ($quiz->passedByUser($user)) {
            return view('quiz.passed', compact('course', 'quiz', 'enrollment'));
        }

        // Get or create attempt
        $attempt = $quiz->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            $attempt = $quiz->attempts()->create([
                'user_id'       => $user->id,
                'enrollment_id' => $enrollment->id,
                'status'        => 'in_progress',
                'started_at'    => now(),
                'total_questions' => $quiz->questions->count(),
            ]);
        }

        $quiz->load(['questions.options']);
        $questions = $quiz->questions()->orderBy('order')->get();

        // Get existing answers for this attempt
        $existingAnswers = $attempt->answers()->pluck('selected_option_id', 'quiz_question_id')->toArray();

        return view('quiz.show', compact('course', 'quiz', 'enrollment', 'attempt', 'questions', 'existingAnswers'));
    }

    /**
     * Save a single answer (AJAX — auto-save as student progresses).
     */
    public function saveAnswer(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'option_id'   => 'required|exists:quiz_options,id',
        ]);

        $option = \App\Models\QuizOption::find($validated['option_id']);

        // Save or update answer
        QuizAnswer::updateOrCreate(
            [
                'quiz_attempt_id'  => $attempt->id,
                'quiz_question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['option_id'],
                'is_correct'         => $option->is_correct,
            ]
        );

        return response()->json(['saved' => true]);
    }

    /**
     * Submit the quiz for grading.
     */
    public function submit(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quiz.show', [$course->slug, $quiz->id])
                ->with('error', 'This attempt has already been submitted.');
        }

        // Enforce min submit time
        $elapsedMinutes = $attempt->started_at->diffInMinutes(now());
        if ($elapsedMinutes < $quiz->min_submit_time) {
            $remaining = $quiz->min_submit_time - $elapsedMinutes;
            return back()->with('error', "Please wait {$remaining} more minute(s) before submitting.");
        }

        // Grade the quiz
        $totalQuestions = $quiz->questions->count();
        $correctAnswers = $attempt->answers()->where('is_correct', true)->count();
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        // Check all questions were answered
        $answeredCount = $attempt->answers()->count();
        if ($answeredCount < $totalQuestions) {
            return back()->with('error', "Please answer all questions before submitting. ({$answeredCount}/{$totalQuestions} answered)");
        }

        $attempt->update([
            'score'           => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'status'          => $passed ? 'passed' : 'failed',
            'submitted_at'    => now(),
        ]);

        return redirect()->route('quiz.result', [$course->slug, $quiz->id, $attempt->id]);
    }

    /**
     * Show quiz results.
     */
    public function result(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        $attempt->load(['answers.question.options', 'answers.selectedOption']);

        $showAnswers = !$attempt->passed && $quiz->show_correct_answers && $quiz->type !== 'final_exam';

        // Find the next lesson after this quiz's section
        $nextLesson = null;
        $nextSection = null;

        if ($attempt->passed) {
            $sections = $course->sections()->orderBy('order')->get();
            $currentSectionIndex = $sections->search(fn($s) => $s->id === $quiz->section_id);

            if ($currentSectionIndex !== false && $currentSectionIndex < $sections->count() - 1) {
                $nextSection = $sections[$currentSectionIndex + 1];
                $nextLesson = $nextSection->lessons()->orderBy('order')->first();
            } elseif ($currentSectionIndex !== false && $quiz->type === 'final_exam') {
                // Final exam passed — course complete
                $nextLesson = null;
            }
        }

        return view('quiz.result', compact('course', 'quiz', 'attempt', 'showAnswers', 'nextLesson', 'nextSection'));
    }

    /**
     * Retake quiz — creates a new attempt.
     */
    public function retake(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        $enrollment = $this->verifyEnrollment($user, $course);
        if (!$enrollment) {
            return redirect()->route('student.courses')->with('error', 'Not enrolled.');
        }

        // Mark old in-progress attempts as failed
        $quiz->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->update(['status' => 'failed']);

        // Create new attempt
        $quiz->attempts()->create([
            'user_id'        => $user->id,
            'enrollment_id'  => $enrollment->id,
            'status'         => 'in_progress',
            'started_at'     => now(),
            'total_questions' => $quiz->questions->count(),
        ]);

        return redirect()->route('quiz.show', [$course->slug, $quiz->id]);
    }

    private function verifyEnrollment($user, Course $course): ?Enrollment
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();
    }
}
