<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'               => User::factory(),
            'course_id'             => Course::factory(),
            'enrollment_id'         => null,
            'payment_gateway'       => 'paystack',
            'transaction_reference' => 'LMS-' . strtoupper(Str::random(8)) . '-' . fake()->unique()->numberBetween(1, 1000000),
            'amount'                => fake()->numberBetween(50000, 200000),
            'currency'              => 'NGN',
            'status'                => 'successful',
            'learning_type'         => 'inclass',
            'paid_at'               => now(),
        ];
    }

    /**
     * A payment that has not yet completed.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'  => 'pending',
            'paid_at' => null,
        ]);
    }
}
