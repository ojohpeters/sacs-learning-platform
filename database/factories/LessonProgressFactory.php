<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonProgress>
 */
class LessonProgressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'enrollment_id' => Enrollment::factory(),
            'seconds_spent' => 0,
            'started_at' => now(),
            'last_heartbeat_at' => now(),
        ];
    }

    /**
     * Enough accumulated time that the lesson can be completed.
     */
    public function satisfied(): static
    {
        return $this->state(fn () => ['seconds_spent' => 100000]);
    }
}
