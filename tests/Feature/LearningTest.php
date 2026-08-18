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

    private function courseWithLesson(int $duration = 120, array $attrs = []): array
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(array_merge([
            'section_id' => $section->id,
            'content_type' => 'video',
            'duration' => $duration,
        ], $attrs));

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

        // Mark complete after minimum lesson time has elapsed
        $this->actingAs($user)
            ->withSession([
                "lesson_start_{$lesson->id}" => now()->subMinutes(2)->toIso8601String(),
            ])
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

    public function test_toggle_complete_blocked_before_minimum_lesson_time(): void
    {
        [$course, $lesson] = $this->courseWithLesson();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->withSession([
                "lesson_start_{$lesson->id}" => now()->subSeconds(10)->toIso8601String(),
            ])
            ->post(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertStatus(422)
            ->assertJsonStructure(['error']);

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
            ->assertJsonPath('requiredSeconds', 600); // video → full length

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $spent = LessonProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->value('seconds_spent');
        $this->assertGreaterThan(0, $spent);
    }

    public function test_repeated_heartbeats_accrue_over_time_until_completable(): void
    {
        // Override to 30s so the test targets the accrual mechanic, not the rule.
        [$course, $lesson] = $this->courseWithLesson(60, ['min_seconds' => 30]);
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->freezeTime();

        // First ping credits one interval (15s).
        $this->actingAs($user)
            ->postJson(route('learning.heartbeat', [$course->slug, $lesson->id]))
            ->assertJson(['secondsSpent' => 15, 'canComplete' => false]);

        // 20 real seconds pass, then another ping — must accrue, not stall at 15.
        $this->travel(20)->seconds();
        $this->actingAs($user)
            ->postJson(route('learning.heartbeat', [$course->slug, $lesson->id]))
            ->assertJson(['canComplete' => true]);

        // Completion is now allowed.
        $this->actingAs($user)
            ->postJson(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['completed' => true]);
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

    public function test_text_lesson_requirement_scales_with_reading_length(): void
    {
        // 400 words at 200 wpm = 120s.
        $long = Lesson::factory()->make([
            'content_type' => 'text',
            'content_body' => str_repeat('word ', 400),
            'min_seconds' => null,
        ]);
        $this->assertSame(120, $long->requiredSeconds());

        // A tiny page falls back to the reading floor.
        $short = Lesson::factory()->make([
            'content_type' => 'text',
            'content_body' => 'Just a few words here.',
            'min_seconds' => null,
        ]);
        $this->assertSame((int) config('learning.min_reading_seconds'), $short->requiredSeconds());
    }

    public function test_video_lesson_requires_its_full_length(): void
    {
        $lesson = Lesson::factory()->make(['content_type' => 'video', 'duration' => 480, 'min_seconds' => null]);

        $this->assertSame(480, $lesson->requiredSeconds());
    }

    public function test_min_seconds_override_is_respected(): void
    {
        $lesson = Lesson::factory()->make(['content_type' => 'video', 'duration' => 600, 'min_seconds' => 45]);

        $this->assertSame(45, $lesson->requiredSeconds());
    }

    public function test_lesson_with_zero_min_seconds_can_be_completed_instantly(): void
    {
        [$course, $lesson] = $this->courseWithLesson(600, ['min_seconds' => 0]);
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // No timer — completes immediately.
        $this->actingAs($user)
            ->postJson(route('learning.toggle-complete', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertJson(['completed' => true]);
    }

    public function test_pdf_lesson_renders_inline_viewer(): void
    {
        [$course, $lesson] = $this->courseWithLesson(0, [
            'content_type' => 'pdf',
            'content_path' => 'course-content/doc.pdf',
        ]);
        $user = User::factory()->create();
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $this->actingAs($user)
            ->get(route('learning.lesson', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertSee('<iframe', false)
            ->assertSee('#toolbar=0', false);
    }

    public function test_video_lesson_disables_download(): void
    {
        [$course, $lesson] = $this->courseWithLesson(120, [
            'content_type' => 'video',
            'content_path' => 'course-content/clip.mp4',
        ]);
        $user = User::factory()->create();
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $this->actingAs($user)
            ->get(route('learning.lesson', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertSee('controlsList="nodownload', false);
    }
}
