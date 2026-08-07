<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningTest extends TestCase
{
    use RefreshDatabase;

    private function courseWithLesson(int $duration = 120): array
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['section_id' => $section->id, 'duration' => $duration]);

        return [$course, $lesson];
    }

    private function satisfyTime(User $user, Lesson $lesson, Enrollment $enrollment): void
    {
        LessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'enrollment_id' => $enrollment->id,
            'seconds_spent' => 100000,
            'started_at' => now(),
        ]);
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
            'user_id' => $user->id,
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
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
        $this->satisfyTime($user, $lesson, $enrollment);

        // Mark complete
        $this->actingAs($user)
            ->post(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['completed' => true, 'progressPercent' => 100]);

        $this->assertDatabaseHas('lesson_completions', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'enrollment_id' => $enrollment->id,
        ]);

        // Unmark
        $this->actingAs($user)
            ->post(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['completed' => false, 'progressPercent' => 0]);

        $this->assertDatabaseMissing('lesson_completions', [
            'user_id' => $user->id,
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

    public function test_cannot_complete_lesson_without_enough_time(): void
    {
        [$course, $lesson] = $this->courseWithLesson(120); // requires 60s
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // No time accrued — completion is refused.
        $this->actingAs($user)
            ->postJson(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertStatus(422)
            ->assertJsonStructure(['error', 'secondsRemaining', 'requiredSeconds']);

        $this->assertDatabaseMissing('lesson_completions', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_heartbeat_accrues_time(): void
    {
        [$course, $lesson] = $this->courseWithLesson(600); // requires 300s
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('learning.heartbeat', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['canComplete' => false])
            ->assertJsonPath('requiredSeconds', 300);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $spent = LessonProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->value('seconds_spent');
        $this->assertGreaterThan(0, $spent);
    }

    public function test_opening_a_lesson_starts_its_clock(): void
    {
        [$course, $lesson] = $this->courseWithLesson();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)->get(route('learning.lesson', [$course->slug, $lesson->id]))->assertOk();

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }
}
