<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RedirectsAfterEnrollment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\PaymentFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    use RedirectsAfterEnrollment;

    /**
     * Step 1: Receive the encrypted token from the main website.
     * Decrypt it, validate the course exists, and show the checkout page.
     */
    public function show(Request $request)
    {
        // 1. Validate token exists
        if (! $request->has('token')) {
            return redirect()->route('courses.catalog')
                ->with('error', 'Invalid checkout link. Please select a course first.');
        }

        // 2. Decrypt the token
        try {
            $payload = Crypt::decrypt($request->token);
        } catch (\Exception $e) {
            return redirect()->route('courses.catalog')
                ->with('error', 'This checkout link is invalid or has expired.');
        }

        // 3. Validate token contents
        if (! isset($payload['course_id']) || ! isset($payload['type'])) {
            return redirect()->route('courses.catalog')
                ->with('error', 'Invalid checkout data.');
        }

        // 4. Check token freshness (prevent replay attacks — token valid for 1 hour)
        if (isset($payload['timestamp']) && (now()->timestamp - $payload['timestamp'] > 3600)) {
            return redirect()->route('courses.catalog')
                ->with('error', 'This checkout link has expired. Please return to the course page and try again.');
        }

        // 5. Find the course
        $course = Course::findOrFail($payload['course_id']);

        // 6. Validate learning type
        $learningType = $payload['type'];
        if (! in_array($learningType, ['inclass', 'sync', 'async'])) {
            return redirect()->route('courses.catalog')
                ->with('error', 'Invalid learning type.');
        }

        // 7. Calculate the price based on learning type
        $price = $this->priceFor($course, $learningType);

        // 8. Store checkout data in session for the next step
        session([
            'checkout_course_id' => $course->id,
            'checkout_learning_type' => $learningType,
            'checkout_price' => $price,
        ]);

        // 9. If user is not logged in, redirect to register with the checkout target preserved
        if (! auth()->check()) {
            return redirect()->route('register', ['redirect_to' => $request->fullUrl()])
                ->with('checkout_message', 'Create your account to continue enrolling in: '.$course->title);
        }

        // 10. User is logged in, show the checkout confirmation
        return view('checkout.show', compact('course', 'learningType', 'price'));
    }

    /**
     * Step 2: User confirms enrollment and initiates payment.
     *
     * Real mode: create a pending payment and hand off to Paystack's hosted
     * checkout. The callback/webhook verify the charge and enroll the buyer.
     * Fake mode (PAYSTACK_FAKE=true): bypass Paystack and fulfill immediately.
     */
    public function initiatePayment(Request $request, PaystackService $paystack, PaymentFulfillmentService $fulfiller)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $courseId = session('checkout_course_id');
        $learningType = session('checkout_learning_type');
        $price = session('checkout_price');

        if (! $courseId || ! $learningType || ! $price) {
            return redirect()->route('courses.catalog')
                ->with('error', 'Checkout session expired. Please try again.');
        }

        $course = Course::findOrFail($courseId);
        $user = auth()->user();

        // Recompute the price server-side — never trust the session amount alone.
        $price = $this->priceFor($course, $learningType);

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('learning_type', $learningType)
            ->first();

        if ($existingEnrollment) {
            $this->clearCheckoutSession();

            return redirect()->route('student.courses')
                ->with('info', 'You are already enrolled in this course.');
        }

        // Generate unique transaction reference
        $reference = 'LMS-'.strtoupper(Str::random(8)).'-'.time();

        // Create a pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_gateway' => 'paystack',
            'transaction_reference' => $reference,
            'amount' => $price,
            'currency' => 'NGN',
            'status' => 'pending',
            'learning_type' => $learningType,
        ]);

        // Local demo mode — skip Paystack entirely.
        if (config('services.paystack.fake')) {
            $fulfiller->fulfill($payment);
            $this->clearCheckoutSession();

            return $this->redirectAfterEnrollment($payment);
        }

        // Real mode — initialize the Paystack transaction and redirect to checkout.
        try {
            $data = $paystack->initialize(
                email: $user->email,
                amountKobo: (int) round($price * 100),
                reference: $reference,
                callbackUrl: route('payment.callback'),
                metadata: [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'learning_type' => $learningType,
                ],
            );
        } catch (\Throwable $e) {
            report($e);
            $payment->update(['status' => 'failed']);

            return redirect()->route('student.courses')
                ->with('error', 'We could not start your payment. Please try again.');
        }

        // Keep the reference so the callback can recover it if Paystack omits it.
        session(['current_payment_reference' => $reference]);

        return redirect()->away($data['authorization_url']);
    }

    /**
     * Server-side price for a course + learning type (base + ₦4,000 registration,
     * with sync/async discounts). Never falls below zero.
     */
    private function priceFor(Course $course, string $learningType): float
    {
        $registrationFee = 4000;
        $basePrice = $course->price;

        $price = match ($learningType) {
            'inclass' => $basePrice + $registrationFee,
            'sync' => $basePrice - 10000 + $registrationFee,
            'async' => $basePrice - 25000 + $registrationFee,
            default => $basePrice,
        };

        return max($price, 0);
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
