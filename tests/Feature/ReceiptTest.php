<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_inclass_receipt(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $payment = Payment::factory()->create([
            'user_id'       => $user->id,
            'course_id'     => $course->id,
            'learning_type' => 'inclass',
            'status'        => 'successful',
        ]);

        $this->actingAs($user)
            ->get(route('receipt.show', $payment->id))
            ->assertOk()
            ->assertViewIs('receipt.show');
    }

    public function test_non_owner_cannot_view_receipt(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $payment = Payment::factory()->create([
            'user_id'       => $owner->id,
            'learning_type' => 'inclass',
            'status'        => 'successful',
        ]);

        $this->actingAs($attacker)
            ->get(route('receipt.show', $payment->id))
            ->assertForbidden();
    }

    public function test_pending_payment_receipt_redirects(): void
    {
        $user = User::factory()->create();
        $payment = Payment::factory()->pending()->create([
            'user_id'       => $user->id,
            'learning_type' => 'inclass',
        ]);

        $this->actingAs($user)
            ->get(route('receipt.show', $payment->id))
            ->assertRedirect(route('student.courses'))
            ->assertSessionHas('error');
    }
}
