<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_courses'    => Course::count(),
            'total_students'   => User::where('role', 'student')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue'     => Payment::where('status', 'successful')->sum('amount'),
            'recent_enrollments' => Enrollment::with(['user', 'course'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_payments' => Payment::with(['user', 'course'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', $stats);
    }
}