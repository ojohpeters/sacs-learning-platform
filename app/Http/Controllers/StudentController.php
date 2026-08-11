<?php

namespace App\Http\Controllers;

use App\Models\LessonCompletion;

class StudentController extends Controller
{
    /**
     * Show the student's enrolled courses with per-course progress.
     */
    public function courses()
    {
        $user = auth()->user();

        $enrollments = $user->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->latest()
            ->get();

        foreach ($enrollments as $enrollment) {
            $lessonIds = $enrollment->course->lessons()->pluck('lessons.id');
            $total = $lessonIds->count();
            $completed = $total > 0
                ? LessonCompletion::where('user_id', $user->id)->whereIn('lesson_id', $lessonIds)->count()
                : 0;

            $enrollment->progress_total = $total;
            $enrollment->progress_completed = $completed;
            $enrollment->progress_percent = $total > 0 ? (int) round($completed / $total * 100) : 0;
        }

        return view('student.courses', compact('enrollments'));
    }
}
