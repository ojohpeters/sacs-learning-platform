<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount(['sections', 'enrollments'])->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'full_description'  => 'required|string',
            'price'             => 'required|numeric|min:0',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published'      => 'boolean',
        ]);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        Course::create([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']),
            'short_description' => $validated['short_description'],
            'full_description'  => $validated['full_description'],
            'price'             => $validated['price'],
            'thumbnail_path'    => $thumbnailPath,
            'is_published'      => $request->has('is_published'),
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'full_description'  => 'required|string',
            'price'             => 'required|numeric|min:0',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published'      => 'boolean',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if it exists and is a local file
            if ($course->thumbnail_path && Storage::disk('public')->exists($course->thumbnail_path)) {
                Storage::disk('public')->delete($course->thumbnail_path);
            }
            $validated['thumbnail_path'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Show the curriculum builder for a specific course.
     */
    public function curriculum(Course $course)
    {
        $course->load(['sections.lessons' => function ($query) {
            $query->orderBy('order');
        }]);

        return view('admin.courses.curriculum', compact('course'));
    }
}