<?php

namespace Tests\Feature;

use App\Mail\EnrollmentConfirmation;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaystackPaymentTest extends TestCase
{
    use RefreshDatabase;

    private string $secret = 'sk_test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.paystack.fake' => false,
            'services.paystack.secret_key' => $this->secret,
            'services.paystack.payment_url' => 'https://api.paystack.co',
        ]);
    }

    private function pendingPayment(string $type = 'async'): Payment
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        return Payment::factory()->pending()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'learning_type' => $type,
            'transaction_reference' => 'LMS-REF-123',
        ]);
    }

    // ---------- Redirect callback ----------

    public function test_callback_verifies_and_enrolls(): void
    {
        Mail::fake();
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success'],
            ]),
        ]);

        $payment = $this->pendingPayment('async');

        $this->actingAs($payment->user)
            ->get(route('payment.callback', ['reference' => $payment->transaction_reference]))
            ->assertRedirect(route('student.courses'));

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'successful']);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $payment->user_id,
            'course_id' => $payment->course_id,
            'status' => 'active',
        ]);
        Mail::assertSent(EnrollmentConfirmation::class);
    }

    public function test_callback_inclass_redirects_to_receipt(): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success'],
            ]),
        ]);

        $payment = $this->pendingPayment('inclass');

        $this->actingAs($payment->user)
            ->get(route('payment.callback', ['reference' => $payment->transaction_reference]))
            ->assertRedirect(route('receipt.show', $payment->id));
    }

    public function test_callback_marks_payment_failed_when_verification_fails(): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'failed'],
            ]),
        ]);

        $payment = $this->pendingPayment();

        $this->actingAs($payment->user)
            ->get(route('payment.callback', ['reference' => $payment->transaction_reference]))
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'failed']);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_callback_without_reference_redirects_with_error(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('payment.callback'))
            ->assertRedirect(route('student.courses'))
            ->assertSessionHas('error');
    }

    // ---------- Webhook ----------

    private function signedWebhook(string $reference, string $status = 'success', string $event = 'charge.success'): array
    {
        $payload = json_encode([
            'event' => $event,
            'data' => ['status' => $status, 'reference' => $reference],
        ]);
        $signature = hash_hmac('sha512', $payload, $this->secret);

        return [$payload, $signature];
    }

    public function test_webhook_with_valid_signature_enrolls(): void
    {
        Mail::fake();
        $payment = $this->pendingPayment('async');
        [$payload, $signature] = $this->signedWebhook($payment->transaction_reference);

        $this->call('POST', route('payment.webhook'), [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertOk();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'successful']);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $payment->user_id,
            'course_id' => $payment->course_id,
        ]);
        Mail::assertSent(EnrollmentConfirmation::class, 1);
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        $payment = $this->pendingPayment();
        [$payload] = $this->signedWebhook($payment->transaction_reference);

        $this->call('POST', route('payment.webhook'), [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => 'wrong-signature',
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertStatus(401);

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'pending']);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_callback_and_webhook_are_idempotent(): void
    {
        Mail::fake();
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success'],
            ]),
        ]);

        $payment = $this->pendingPayment('async');

        // Callback first...
        $this->actingAs($payment->user)
            ->get(route('payment.callback', ['reference' => $payment->transaction_reference]))
            ->assertRedirect(route('student.courses'));

        // ...then the webhook fires for the same transaction.
        [$payload, $signature] = $this->signedWebhook($payment->transaction_reference);
        $this->call('POST', route('payment.webhook'), [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
        ], $payload)->assertOk();

        // Exactly one enrollment, one payment, one email — no duplicates.
        $this->assertDatabaseCount('enrollments', 1);
        $this->assertDatabaseCount('payments', 1);
        Mail::assertSent(EnrollmentConfirmation::class, 1);
    }
}
