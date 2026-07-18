<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'course'])
            ->latest()
            ->paginate(20);

        $totalRevenue = Payment::where('status', 'successful')->sum('amount');
        $totalPending = Payment::where('status', 'pending')->count();
        $totalFailed = Payment::where('status', 'failed')->count();

        return view('admin.payments.index', compact('payments', 'totalRevenue', 'totalPending', 'totalFailed'));
    }
}