<?php

namespace App\Services;

use App\Mail\EnrollmentConfirmation;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PaymentFulfillmentService
{
    /**
     * Mark a payment successful and enroll the buyer.
     *
     * Idempotent: the Paystack callback and webhook can both fire for the same
     * transaction, so calling this twice must not double-enroll or double-email.
     */
    public function fulfill(Payment $payment): Enrollment
    {
        return DB::transaction(function () use ($payment) {
            // Lock the row to avoid a race between a concurrent callback + webhook.
            $payment = Payment::whereKey($payment->getKey())->lockForUpdate()->first();

            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $payment->user_id,
                    'course_id' => $payment->course_id,
                    'learning_type' => $payment->learning_type,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]
            );

            $freshlyEnrolled = $enrollment->wasRecentlyCreated;

            if ($payment->status !== 'successful') {
                $payment->update([
                    'status' => 'successful',
                    'paid_at' => $payment->paid_at ?? now(),
                    'enrollment_id' => $enrollment->id,
                ]);
            } elseif ($payment->enrollment_id === null) {
                $payment->update(['enrollment_id' => $enrollment->id]);
            }

            // Send the confirmation only the first time the buyer is enrolled.
            // Never let a mail failure roll back the enrollment.
            if ($freshlyEnrolled) {
                try {
                    Mail::to($payment->user)->send(new EnrollmentConfirmation($enrollment));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            return $enrollment;
        });
    }
}
