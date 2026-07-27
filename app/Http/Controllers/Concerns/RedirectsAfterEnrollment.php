<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Payment;
use Illuminate\Http\RedirectResponse;

trait RedirectsAfterEnrollment
{
    /**
     * Send the buyer to the right place after a successful enrollment,
     * based on the learning type they purchased.
     */
    protected function redirectAfterEnrollment(Payment $payment): RedirectResponse
    {
        return match ($payment->learning_type) {
            'inclass' => redirect()->route('receipt.show', $payment->id)
                ->with('success', "Payment successful! Here's what to do next."),
            'sync' => redirect()->route('learning.course', $payment->course->slug)
                ->with('success', 'Enrollment successful! Join your live sessions below.'),
            default => redirect()->route('student.courses')
                ->with('success', 'Enrollment successful! Welcome to '.$payment->course->title.'.'),
        };
    }
}
