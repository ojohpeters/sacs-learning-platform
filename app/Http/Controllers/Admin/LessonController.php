<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Store a new section.
     */
    public function storeSection(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $order = $course->sections()->max('order') + 1;

        Section::create([
            'course_id' => $course->id,
            'title'     => $validated['title'],
            'order'     => $order,
        ]);

        return back()->with('success', 'Section added.');
    }

    /**
     * Update a section.
     */
    public function updateSection(Request $request, Section $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->update($validated);

        return back()->with('success', 'Section updated.');
    }

    /**
     * Delete a section and its lessons.
     */
    public function destroySection(Section $section)
    {
        $section->delete();

        return back()->with('success', 'Section and its lessons deleted.');
    }

    /**
     * Store a new lesson.
     */
    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content_type' => 'required|in:video,text,image,pdf',
            'content_file' => 'nullable|file|max:50000|mimes:mp4,mov,avi,jpg,jpeg,png,gif,webp,pdf',
            'content_body' => 'nullable|string',
            'duration'     => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',
        ]);

        $order = $section->lessons()->max('order') + 1;

        // Handle file upload
        $contentPath = null;
        if ($request->hasFile('content_file')) {
            $contentPath = $request->file('content_file')->store('course-content', 'public');
        }

        Lesson::create([
            'section_id'      => $section->id,
            'title'           => $validated['title'],
            'content_type'    => $validated['content_type'],
            'content_path'    => $contentPath,
            'content_body'    => $validated['content_body'] ?? null,
            'duration'        => $validated['duration'] ?? 0,
            'order'           => $order,
            'is_free_preview' => $request->has('is_free_preview'),
        ]);

        return back()->with('success', 'Lesson added successfully.');
    }
    /**
     * Update a lesson.
     */
    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content_type' => 'required|in:video,text,image,pdf',
            'content_file' => 'nullable|file|max:50000|mimes:mp4,mov,avi,jpg,jpeg,png,gif,webp,pdf',
            'content_body' => 'nullable|string',
            'duration'     => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('content_file')) {
            // Delete old file if exists
            if ($lesson->content_path && \Storage::disk('public')->exists($lesson->content_path)) {
                \Storage::disk('public')->delete($lesson->content_path);
            }
            $validated['content_path'] = $request->file('content_file')->store('course-content', 'public');
        } else {
            // Keep existing path
            $validated['content_path'] = $lesson->content_path;
        }

        $validated['is_free_preview'] = $request->has('is_free_preview');
        $lesson->update($validated);

        return back()->with('success', 'Lesson updated.');
    }

    /**
     * Delete a lesson.
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }
}
