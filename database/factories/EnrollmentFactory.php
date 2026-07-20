<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'course_id'     => Course::factory(),
            'learning_type' => 'async',
            'status'        => 'active',
            'enrolled_at'   => now(),
            'completed_at'  => null,
        ];
    }
}
