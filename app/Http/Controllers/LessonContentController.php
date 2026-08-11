<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonContentController extends Controller
{
    /**
     * Stream a lesson's uploaded file with access control.
     *
     * Free-preview lessons are public. Everything else requires an active
     * enrollment in the course. Files live on the private "local" disk so they
     * cannot be reached directly via the public storage symlink.
     */
    public function stream(Course $course, Lesson $lesson)
    {
        // Lesson must belong to this course.
        if ($lesson->section->course_id !== $course->id) {
            abort(404);
        }

        // External URLs (e.g. seeded/hosted media) are not access-controlled here.
        if ($lesson->content_path && Str::startsWith($lesson->content_path, 'http')) {
            return redirect()->away($lesson->content_path);
        }

        // Access gate: free previews are open; otherwise require an active enrollment.
        if (! $lesson->is_free_preview) {
            $user = auth()->user();

            if (! $user) {
                abort(403, 'You must be enrolled to view this content.');
            }

            $enrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();

            if (! $enrolled) {
                abort(403, 'You must be enrolled to view this content.');
            }
        }

        if (! $lesson->content_path || ! Storage::disk('local')->exists($lesson->content_path)) {
            abort(404);
        }

        // response()->file() emits a BinaryFileResponse, which supports HTTP Range
        // requests — required for seeking within video. Serve it inline (viewed
        // in the browser, not downloaded) and stop content-type sniffing.
        return response()->file(Storage::disk('local')->path($lesson->content_path), [
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
