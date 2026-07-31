<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RedirectsAfterEnrollment;
use App\Models\Payment;
use App\Services\PaymentFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    use RedirectsAfterEnrollment;

    /**
     * Handle the browser redirect back from Paystack after a payment attempt.
     */
    public function handle(Request $request, PaystackService $paystack, PaymentFulfillmentService $fulfiller)
    {
        $reference = $request->query('reference') ?? session('current_payment_reference');

        if (! $reference) {
            return redirect()->route('student.courses')
                ->with('error', 'No payment reference found.');
        }

        $payment = Payment::where('transaction_reference', $reference)->first();

        if (! $payment) {
            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        // If a webhook already fulfilled this payment, just send the buyer on.
        if ($payment->status === 'successful') {
            $this->clearCheckoutSession();

            return $this->redirectAfterEnrollment($payment);
        }

        if ($paystack->verify($reference)) {
            $fulfiller->fulfill($payment);
            $this->clearCheckoutSession();

            return $this->redirectAfterEnrollment($payment);
        }

        // Verification failed.
        $payment->update(['status' => 'failed']);

        return redirect()->route('courses.catalog')
            ->with('error', 'Payment was not successful. Please try again.');
    }

    private function clearCheckoutSession(): void
    {
        session()->forget([
            'checkout_course_id',
            'checkout_learning_type',
            'checkout_price',
            'current_payment_reference',
        ]);
    }
}
