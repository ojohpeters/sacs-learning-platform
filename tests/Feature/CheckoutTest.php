<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function token(Course $course, string $type = 'async', ?int $timestamp = null): string
    {
        return Crypt::encrypt([
            'course_id' => $course->id,
            'type'      => $type,
            'timestamp' => $timestamp ?? now()->timestamp,
        ]);
    }

    public function test_checkout_without_token_redirects_to_catalog(): void
    {
        $this->get('/checkout')
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    public function test_checkout_with_invalid_token_redirects_to_catalog(): void
    {
        // Previously this path threw RouteNotFoundException (courses.index did not exist).
        $this->get('/checkout?token=not-a-valid-token')
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    public function test_checkout_with_expired_token_redirects_to_catalog(): void
    {
        $course = Course::factory()->create();
        $expired = $this->token($course, 'async', now()->subHours(2)->timestamp);

        $this->get('/checkout?token=' . urlencode($expired))
            ->assertRedirect(route('courses.catalog'))
            ->assertSessionHas('error');
    }

    public function test_guest_with_valid_token_is_redirected_to_register(): void
    {
        $course = Course::factory()->create();

        $this->get('/checkout?token=' . urlencode($this->token($course)))
            ->assertRedirectContains('/register');
    }

    public function test_authenticated_user_sees_checkout_page(): void
    {
        $course = Course::factory()->create(['price' => 100000]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/checkout?token=' . urlencode($this->token($course, 'async')))
            ->assertOk()
            ->assertViewIs('checkout.show')
            ->assertSee($course->title);
    }

    public function test_initiate_payment_enrolls_user_in_test_mode(): void
    {
        $course = Course::factory()->create(['price' => 100000]);
        $user = User::factory()->create();

        // async price = 100000 - 25000 + 4000 = 79000
        $response = $this->actingAs($user)
            ->withSession([
                'checkout_course_id'     => $course->id,
                'checkout_learning_type' => 'async',
                'checkout_price'         => 79000,
            ])
            ->post('/checkout/pay');

        $response->assertRedirect(route('student.courses'));

        $this->assertDatabaseHas('payments', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'status'    => 'successful',
            'amount'    => 79000,
        ]);

        $this->assertDatabaseHas('enrollments', [
            'user_id'       => $user->id,
            'course_id'     => $course->id,
            'learning_type' => 'async',
            'status'        => 'active',
        ]);
    }

    public function test_inclass_payment_redirects_to_receipt(): void
    {
        $course = Course::factory()->create(['price' => 100000]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession([
                'checkout_course_id'     => $course->id,
                'checkout_learning_type' => 'inclass',
                'checkout_price'         => 104000,
            ])
            ->post('/checkout/pay');

        $payment = Payment::where('user_id', $user->id)->firstOrFail();
        $response->assertRedirect(route('receipt.show', $payment->id));
    }

    public function test_already_enrolled_user_is_not_charged_again(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id'       => $user->id,
            'course_id'     => $course->id,
            'learning_type' => 'async',
        ]);

        $this->actingAs($user)
            ->withSession([
                'checkout_course_id'     => $course->id,
                'checkout_learning_type' => 'async',
                'checkout_price'         => 79000,
            ])
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
}
