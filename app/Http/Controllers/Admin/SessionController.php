<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Course $course)
    {
        $sessions = $course->sessions()->orderBy('session_date')->orderBy('start_time')->get();
        return view('admin.sessions.index', compact('course', 'sessions'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|in:sync',
            'session_date' => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required',
            'meeting_link' => 'nullable|url|max:500',
        ]);

        $validated['course_id'] = $course->id;
        $validated['status'] = 'upcoming';

        Session::create($validated);

        return back()->with('success', 'Session added successfully.');
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'session_date'   => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'meeting_link'   => 'nullable|url|max:500',
            'recording_link' => 'nullable|url|max:500',
            'status'         => 'required|in:upcoming,live,completed,cancelled',
        ]);

        $session->update($validated);

        return back()->with('success', 'Session updated.');
    }

    public function destroy(Session $session)
    {
        $session->delete();
        return back()->with('success', 'Session deleted.');
    }
}