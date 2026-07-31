<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title'             => $title,
            'slug'              => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 100000),
            'short_description' => fake()->sentence(),
            'full_description'  => fake()->paragraph(),
            'price'             => fake()->numberBetween(50000, 200000),
            'thumbnail_path'    => null,
            'is_published'      => true,
        ];
    }

    /**
     * Indicate the course is a draft (not published).
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
