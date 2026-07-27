<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Paystack webhooks (the reliable server-to-server signal — fires
     * even if the buyer closes the browser before the redirect callback).
     */
    public function handle(Request $request, PaystackService $paystack, PaymentFulfillmentService $fulfiller)
    {
        $payload = $request->getContent();

        // Reject anything not signed with our secret key.
        if (! $paystack->isValidSignature($payload, $request->header('x-paystack-signature'))) {
            abort(401, 'Invalid signature.');
        }

        $event = json_decode($payload, true) ?: [];

        if (($event['event'] ?? null) === 'charge.success'
            && ($event['data']['status'] ?? null) === 'success') {

            $reference = $event['data']['reference'] ?? null;
            $payment = $reference
                ? Payment::where('transaction_reference', $reference)->first()
                : null;

            if ($payment && $payment->status !== 'successful') {
                $fulfiller->fulfill($payment);
            }
        }

        // Always acknowledge with 200 so Paystack stops retrying.
        return response()->json(['status' => 'ok']);
    }
}
