<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningTest extends TestCase
{
    use RefreshDatabase;

    private function courseWithLesson(): array
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['section_id' => $section->id]);

        return [$course, $lesson];
    }

    public function test_guest_cannot_access_course_player(): void
    {
        [$course] = $this->courseWithLesson();

        $this->get(route('learning.course', $course->slug))
            ->assertRedirect(route('login'));
    }

    public function test_non_enrolled_user_cannot_access_course_player(): void
    {
        [$course] = $this->courseWithLesson();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('learning.course', $course->slug))
            ->assertRedirect(route('student.courses'))
            ->assertSessionHas('error');
    }

    public function test_enrolled_user_can_access_course_player(): void
    {
        [$course] = $this->courseWithLesson();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('learning.course', $course->slug))
            ->assertOk()
            ->assertViewIs('learning.player');
    }

    public function test_toggle_complete_marks_and_unmarks_lesson(): void
    {
        [$course, $lesson] = $this->courseWithLesson();
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        // Mark complete
        $this->actingAs($user)
            ->post(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['completed' => true, 'progressPercent' => 100]);

        $this->assertDatabaseHas('lesson_completions', [
            'user_id'       => $user->id,
            'lesson_id'     => $lesson->id,
            'enrollment_id' => $enrollment->id,
        ]);

        // Unmark
        $this->actingAs($user)
            ->post(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['completed' => false, 'progressPercent' => 0]);

        $this->assertDatabaseMissing('lesson_completions', [
            'user_id'   => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_non_enrolled_user_cannot_toggle_completion(): void
    {
        [$course, $lesson] = $this->courseWithLesson();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertForbidden();
    }
}
