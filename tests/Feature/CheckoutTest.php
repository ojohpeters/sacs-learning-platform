<?php

namespace Tests\Feature;

use App\Mail\EnrollmentConfirmation;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function token(Course $course, string $type = 'async', ?int $timestamp = null): string
    {
        return Crypt::encrypt([
            'course_id' => $course->id,
            'type' => $type,
            'timestamp' => $timestamp ?? now()->timestamp,
        ]);
    }

    private function checkoutSession(Course $course, string $type = 'async', float $price = 79000): array
    {
        return [
            'checkout_course_id' => $course->id,
            'checkout_learning_type' => $type,
            'checkout_price' => $price,
        ];
    }

    // ---------- Step 1: show() token handling ----------

    public function test_checkout_without_token_redirects_to_catalog(): void
    {
        $this->get('/checkout')
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    public function test_checkout_with_invalid_token_redirects_to_catalog(): void
    {
        $this->get('/checkout?token=not-a-valid-token')
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    public function test_checkout_with_expired_token_redirects_to_catalog(): void
    {
        $course = Course::factory()->create();
        $expired = $this->token($course, 'async', now()->subHours(2)->timestamp);

        $this->get('/checkout?token='.urlencode($expired))
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    public function test_guest_with_valid_token_is_redirected_to_register(): void
    {
        $course = Course::factory()->create();

        $this->get('/checkout?token='.urlencode($this->token($course)))
            ->assertRedirectContains('/register');
    }

    public function test_authenticated_user_sees_checkout_page(): void
    {
        $course = Course::factory()->create(['price' => 100000, 'async_price' => 79000]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/checkout?token='.urlencode($this->token($course, 'async')))
            ->assertOk()
            ->assertViewIs('checkout.show')
            ->assertSee($course->title);
    }

    // ---------- Step 2: real Paystack mode ----------

    public function test_initiate_payment_redirects_to_paystack(): void
    {
        config(['services.paystack.fake' => false]);
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/xyz123',
                    'access_code' => 'ac_123',
                    'reference' => 'ref_123',
                ],
            ]),
        ]);

        $course = Course::factory()->create(['price' => 100000, 'async_price' => 79000]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession($this->checkoutSession($course))
            ->post('/checkout/pay');

        $response->assertRedirect('https://checkout.paystack.com/xyz123');

        // Payment is pending and the buyer is NOT yet enrolled — that happens
        // only after Paystack confirms the charge.
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'amount' => 79000,
        ]);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_paystack_initialization_failure_marks_payment_failed(): void
    {
        config(['services.paystack.fake' => false]);
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'Invalid key',
            ], 401),
        ]);

        $course = Course::factory()->create(['price' => 100000, 'async_price' => 79000]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession($this->checkoutSession($course))
            ->post('/checkout/pay')
            ->assertRedirect(route('student.courses'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('payments', ['user_id' => $user->id, 'status' => 'failed']);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_already_enrolled_user_is_not_charged_again(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'learning_type' => 'async',
        ]);

        $this->actingAs($user)
            ->withSession($this->checkoutSession($course))
            ->post('/checkout/pay')
            ->assertRedirect(route('student.courses'));

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('enrollments', 1);
    }

    public function test_initiate_payment_requires_active_checkout_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/checkout/pay')
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    // ---------- Step 2: fake mode (local demo) ----------

    public function test_fake_mode_enrolls_immediately(): void
    {
        config(['services.paystack.fake' => true]);
        Mail::fake();

        $course = Course::factory()->create(['price' => 100000, 'async_price' => 79000]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession($this->checkoutSession($course))
            ->post('/checkout/pay')
            ->assertRedirect(route('student.courses'));

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'status' => 'successful',
            'amount' => 79000,
        ]);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'learning_type' => 'async',
            'status' => 'active',
        ]);

        $payment = Payment::where('user_id', $user->id)->firstOrFail();
        $enrollment = Enrollment::where('user_id', $user->id)->firstOrFail();
        $this->assertSame($enrollment->id, $payment->enrollment_id);

        Mail::assertSent(EnrollmentConfirmation::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_fake_mode_inclass_redirects_to_receipt(): void
    {
        config(['services.paystack.fake' => true]);

        $course = Course::factory()->create(['price' => 100000, 'async_price' => 79000]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession($this->checkoutSession($course, 'inclass', 104000))
            ->post('/checkout/pay');

        $payment = Payment::where('user_id', $user->id)->firstOrFail();
        $response->assertRedirect(route('receipt.show', $payment->id));
    }

    public function test_enrollment_email_template_renders(): void
    {
        $enrollment = Enrollment::factory()->create();

        $html = (new EnrollmentConfirmation($enrollment))->render();

        $this->assertStringContainsString($enrollment->course->title, $html);
        $this->assertStringContainsString($enrollment->user->name, $html);
    }
}
