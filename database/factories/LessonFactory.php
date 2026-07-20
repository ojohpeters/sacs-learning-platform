<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'section_id'      => Section::factory(),
            'title'           => fake()->sentence(3),
            'content_type'    => 'text',
            'content_path'    => null,
            'content_body'    => fake()->paragraph(),
            'duration'        => fake()->numberBetween(60, 900),
            'order'           => 1,
            'is_free_preview' => false,
        ];
    }

    /**
     * Indicate the lesson is a free preview (accessible without enrollment).
     */
    public function preview(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free_preview' => true,
        ]);
    }
}
