<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Step 1: Receive the encrypted token from the main website.
     * Decrypt it, validate the course exists, and show the checkout page.
     */
    public function show(Request $request)
    {
        // 1. Validate token exists
        if (!$request->has('token')) {
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
        if (!isset($payload['course_id']) || !isset($payload['type'])) {
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
        if (!in_array($learningType, ['inclass', 'sync', 'async'])) {
            return redirect()->route('courses.catalog')
                ->with('error', 'Invalid learning type.');
        }

        // 7. Calculate the price based on learning type
        $registrationFee = 4000; // Fixed registration fee
        $basePrice = $course->price;

        $price = match($learningType) {
            'inclass' => $basePrice + $registrationFee,
            'sync'    => $basePrice - 10000 + $registrationFee,
            'async'   => $basePrice - 25000 + $registrationFee,
            default   => $basePrice,
        };

        // Ensure price is not negative
        $price = max($price, 0);

        // 8. Store checkout data in session for the next step
        session([
            'checkout_course_id'   => $course->id,
            'checkout_learning_type' => $learningType,
            'checkout_price'       => $price,
        ]);

        // 9. If user is not logged in, redirect to register with the checkout target preserved
        if (!auth()->check()) {
            return redirect()->route('register', ['redirect_to' => $request->fullUrl()])
                ->with('checkout_message', 'Create your account to continue enrolling in: ' . $course->title);
        }

        // 10. User is logged in, show the checkout confirmation
        return view('checkout.show', compact('course', 'learningType', 'price'));
    }

    /**
     * Step 2: User confirms enrollment and initiates payment.
     */
    public function initiatePayment(Request $request)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $courseId = session('checkout_course_id');
    $learningType = session('checkout_learning_type');
    $price = session('checkout_price');

    if (!$courseId || !$learningType || !$price) {
        return redirect()->route('courses.catalog')
            ->with('error', 'Checkout session expired. Please try again.');
    }

    $course = Course::findOrFail($courseId);
    $user = auth()->user();

    // Check if already enrolled
    $existingEnrollment = Enrollment::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->where('learning_type', $learningType)
        ->first();

    if ($existingEnrollment) {
        session()->forget(['checkout_course_id', 'checkout_learning_type', 'checkout_price']);
        return redirect()->route('student.courses')
            ->with('info', 'You are already enrolled in this course.');
    }

    // Generate unique transaction reference
    $reference = 'LMS-' . strtoupper(Str::random(8)) . '-' . time();

    // Create a pending payment record
    $payment = Payment::create([
        'user_id'              => $user->id,
        'course_id'            => $course->id,
        'payment_gateway'      => 'paystack',
        'transaction_reference' => $reference,
        'amount'               => $price,
        'currency'             => 'NGN',
        'status'               => 'pending',
        'learning_type'        => $learningType,
    ]);

    // ==========================================
    // TEST MODE: Skip Paystack entirely
    // ==========================================
    // Mark payment as successful
    $payment->update([
        'status' => 'successful',
        'paid_at' => now(),
    ]);

    // Create enrollment directly
    Enrollment::firstOrCreate(
        [
            'user_id'       => $user->id,
            'course_id'     => $course->id,
            'learning_type' => $learningType,
        ],
        [
            'status'      => 'active',
            'enrolled_at' => now(),
        ]
    );

    // Clear checkout session
    session()->forget(['checkout_course_id', 'checkout_learning_type', 'checkout_price']);

    // Redirect based on learning type
    if ($learningType === 'inclass') {
        // In-Class: Show receipt page with instructions
        return redirect()->route('receipt.show', ['payment' => $payment->id])
            ->with('success', 'Payment successful! Here\'s what to do next.');
    } elseif ($learningType === 'sync') {
        // Synchronous: Go to course player with live sessions
        return redirect()->route('learning.course', $course->slug)
            ->with('success', 'Enrollment successful! Join your live sessions below.');
    } else {
        // Asynchronous: Go to my courses
        return redirect()->route('student.courses')
            ->with('success', 'Enrollment successful! Welcome to ' . $course->title . '.');
    }
}
    /**
     * Build the Paystack payment URL.
     */
    private function buildPaystackUrl($user, $course, $price, $reference)
    {
        $paystackPublicKey = config('services.paystack.public_key');
        $callbackUrl = route('payment.callback');

        $params = http_build_query([
            'public_key'  => $paystackPublicKey,
            'email'       => $user->email,
            'amount'      => $price * 100, 
            'currency'    => 'NGN',
            'reference'   => $reference,
            'callback_url' => $callbackUrl,
            'metadata'    => json_encode([
                'user_id'       => $user->id,
                'course_id'     => $course->id,
                'learning_type' => session('checkout_learning_type'),
            ]),
        ]);

        return 'https://checkout.paystack.com/?' . $params;
    }
}