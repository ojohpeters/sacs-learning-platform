<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Section;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Show all quizzes for a course.
     */
    public function index(Course $course)
    {
        $course->load(['sections.quiz.questions.options', 'sections.quiz.attempts']);
        
        return view('admin.quizzes.index', compact('course'));
    }

    /**
     * Show form to create a quiz for a section.
     */
    public function create(Section $section)
    {
        // Ensure no quiz already exists for this section
        if ($section->quiz) {
            return redirect()->route('admin.quizzes.edit', $section->quiz)
                ->with('info', 'This section already has a quiz. You can edit it here.');
        }

        return view('admin.quizzes.create', compact('section'));
    }

    /**
     * Store a new quiz.
     */
    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:section_quiz,final_exam',
            'description'      => 'nullable|string',
            'passing_score'    => 'required|integer|min:1|max:100',
            'time_limit'       => 'nullable|integer|min:1',
            'min_submit_time'  => 'required|integer|min:1',
            'show_correct_answers' => 'boolean',
        ]);

        $validated['section_id'] = $section->id;
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['is_active'] = true;

        $quiz = Quiz::create($validated);

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Quiz created. Now add some questions.');
    }

    /**
     * Show form to edit a quiz (add/edit/delete questions).
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load(['questions.options', 'section.course']);
        
        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Update quiz settings.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:section_quiz,final_exam',
            'description'      => 'nullable|string',
            'passing_score'    => 'required|integer|min:1|max:100',
            'time_limit'       => 'nullable|integer|min:1',
            'min_submit_time'  => 'required|integer|min:1',
            'show_correct_answers' => 'boolean',
            'is_active'        => 'boolean',
        ]);

        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['is_active'] = $request->has('is_active');

        $quiz->update($validated);

        return back()->with('success', 'Quiz settings updated.');
    }

    /**
     * Delete a quiz and all its questions/options.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index', $quiz->section->course_id)
            ->with('success', 'Quiz deleted.');
    }

    /**
     * Add a question to a quiz.
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options'       => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required|integer|min:0',
        ]);

        $order = $quiz->questions()->max('order') + 1;

        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'order'         => $order,
        ]);

        foreach ($validated['options'] as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct'  => $index == $validated['correct_option'],
                'order'       => $index + 1,
            ]);
        }

        return back()->with('success', 'Question added.');
    }

    /**
     * Update a question.
     */
    public function updateQuestion(Request $request, QuizQuestion $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options'       => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required|integer|min:0',
        ]);

        $question->update(['question_text' => $validated['question_text']]);

        // Delete old options and recreate
        $question->options()->delete();

        foreach ($validated['options'] as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct'  => $index == $validated['correct_option'],
                'order'       => $index + 1,
            ]);
        }

        return back()->with('success', 'Question updated.');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(QuizQuestion $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted.');
    }
}