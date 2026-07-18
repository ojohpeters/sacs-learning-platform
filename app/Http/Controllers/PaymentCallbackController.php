<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    /**
     * Handle the return from Paystack after payment attempt.
     */
    public function handle(Request $request)
    {
        $reference = $request->get('reference');
        
        if (!$reference) {
            return redirect()->route('student.courses')
                ->with('error', 'No payment reference found.');
        }

        // Find the payment record
        $payment = Payment::where('transaction_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        // Verify payment with Paystack
        $verified = $this->verifyPaystackPayment($reference);

        if ($verified) {
            // Update payment status
            $payment->update([
                'status' => 'successful',
                'paid_at' => now(),
            ]);

            // Create enrollment
            Enrollment::firstOrCreate(
                [
                    'user_id'       => $payment->user_id,
                    'course_id'     => $payment->course_id,
                    'learning_type' => $payment->learning_type,
                ],
                [
                    'status'      => 'active',
                    'enrolled_at' => now(),
                ]
            );

            // Clear checkout session
            session()->forget(['checkout_course_id', 'checkout_learning_type', 'checkout_price', 'current_payment_id']);

            return redirect()->route('student.courses')
                ->with('success', 'Payment successful! You are now enrolled in the course.');
        }

        // Payment failed
        $payment->update(['status' => 'failed']);

        return redirect()->route('checkout.show', ['token' => session('last_token')])
            ->with('error', 'Payment was not successful. Please try again.');
    }

    /**
     * Verify payment with Paystack API.
     */
    private function verifyPaystackPayment($reference)
    {
        $secretKey = config('services.paystack.secret_key');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://api.paystack.co/transaction/verify/" . $reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer " . $secretKey,
                "Cache-Control: no-cache",
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return isset($data['status']) && $data['status'] === true 
                && $data['data']['status'] === 'success';
        }

        return false;
    }
}