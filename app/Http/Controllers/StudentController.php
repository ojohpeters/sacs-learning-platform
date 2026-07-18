<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Show the student's enrolled courses.
     */
    public function courses()
    {
        $enrollments = auth()->user()
            ->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('student.courses', compact('enrollments'));
    }
}