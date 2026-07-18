<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class ReceiptController extends Controller
{
    public function show(Payment $payment)
    {
        // Ensure user owns this payment
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure payment is successful
        if ($payment->status !== 'successful') {
            return redirect()->route('student.courses')
                ->with('error', 'Payment not confirmed yet.');
        }

        // Ensure it's an in-class enrollment
        if ($payment->learning_type !== 'inclass') {
            return redirect()->route('student.courses');
        }

        $payment->load('course', 'user');

        return view('receipt.show', compact('payment'));
    }

    public function download(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($payment->status !== 'successful') {
            abort(404);
        }

        $payment->load('course', 'user');

        $html = view('receipt.pdf', compact('payment'))->render();

        // Simple HTML-to-PDF using browser print
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline');
    }
}