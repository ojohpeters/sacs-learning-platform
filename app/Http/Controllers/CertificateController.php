<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;

class CertificateController extends Controller
{
    /**
     * Show a completion certificate — only once every lesson in the course
     * has been completed by the enrolled student.
     */
    public function show(Course $course)
    {
        $user = auth()->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if (! $enrollment) {
            return redirect()->route('student.courses')
                ->with('error', 'You are not enrolled in this course.');
        }

        $lessonIds = $course->lessons()->pluck('lessons.id');
        $total = $lessonIds->count();
        $completed = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->count();

        if ($total === 0 || $completed < $total) {
            return redirect()->route('learning.course', $course->slug)
                ->with('error', 'Complete all lessons to unlock your certificate.');
        }

        // Issue (or reuse) the certificate's public verification code.
        $enrollment->issueCertificate();

        $completedAt = $enrollment->certificate_issued_at ?? $enrollment->completed_at ?? now();
        $verifyUrl = route('certificate.verify', $enrollment->certificate_code);

        return view('certificate.show', compact('course', 'user', 'enrollment', 'completedAt', 'verifyUrl'));
    }
}
