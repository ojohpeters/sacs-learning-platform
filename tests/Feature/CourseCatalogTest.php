<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_published_courses_only(): void
    {
        $published = Course::factory()->create(['title' => 'Published Course']);
        $draft = Course::factory()->unpublished()->create(['title' => 'Hidden Draft Course']);

        $this->get('/courses')
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_published_course_detail_is_viewable(): void
    {
        $course = Course::factory()->create();

        $this->get(route('courses.show', $course->slug))
            ->assertOk()
            ->assertSee($course->title);
    }

    public function test_unpublished_course_detail_returns_404(): void
    {
        $course = Course::factory()->unpublished()->create();

        $this->get(route('courses.show', $course->slug))
            ->assertNotFound();
    }

    public function test_free_preview_lesson_is_accessible_without_enrollment(): void
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->preview()->create(['section_id' => $section->id]);

        $this->get(route('courses.preview', [$course->slug, $lesson->id]))
            ->assertOk();
    }

    public function test_non_preview_lesson_is_forbidden_without_enrollment(): void
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['section_id' => $section->id]);

        $this->get(route('courses.preview', [$course->slug, $lesson->id]))
            ->assertForbidden();
    }
}
