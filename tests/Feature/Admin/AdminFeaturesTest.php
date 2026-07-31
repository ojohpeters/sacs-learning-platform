<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Section;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    // ---- Read-only admin pages render ----

    public function test_admin_index_pages_render(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create();
        Section::factory()->create(['course_id' => $course->id]);
        Payment::factory()->create();

        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.courses.index'))->assertOk();
        $this->get(route('admin.courses.create'))->assertOk();
        $this->get(route('admin.courses.edit', $course))->assertOk();
        $this->get(route('admin.courses.curriculum', $course))->assertOk();
        $this->get(route('admin.sessions.index', $course))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.payments.index'))->assertOk();
    }

    // ---- Course update (edit path) ----

    public function test_admin_can_update_course(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create(['title' => 'Old Title', 'price' => 1000]);

        $this->actingAs($admin)
            ->put(route('admin.courses.update', $course), [
                'title'             => 'New Title',
                'short_description' => 'Updated short.',
                'full_description'  => 'Updated full description.',
                'price'             => 2500,
                'is_published'      => '1',
            ])
            ->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas('courses', [
            'id'    => $course->id,
            'title' => 'New Title',
            'slug'  => 'new-title',
            'price' => 2500,
        ]);
    }

    // ---- Sections CRUD ----

    public function test_admin_can_create_update_delete_section(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create();

        // Create
        $this->actingAs($admin)
            ->post(route('admin.sections.store', $course), ['title' => 'Module One'])
            ->assertRedirect();
        $this->assertDatabaseHas('sections', ['course_id' => $course->id, 'title' => 'Module One']);
        $section = Section::where('title', 'Module One')->firstOrFail();

        // Update
        $this->actingAs($admin)
            ->put(route('admin.sections.update', $section), ['title' => 'Module One (Renamed)'])
            ->assertRedirect();
        $this->assertDatabaseHas('sections', ['id' => $section->id, 'title' => 'Module One (Renamed)']);

        // Delete
        $this->actingAs($admin)
            ->delete(route('admin.sections.destroy', $section))
            ->assertRedirect();
        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    // ---- Lessons CRUD ----

    public function test_admin_can_create_update_delete_lesson(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);

        // Create
        $this->actingAs($admin)
            ->post(route('admin.lessons.store', $section), [
                'title'        => 'Lesson A',
                'content_type' => 'text',
                'content_body' => 'Some body content.',
                'duration'     => 120,
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('lessons', ['section_id' => $section->id, 'title' => 'Lesson A']);
        $lesson = Lesson::where('title', 'Lesson A')->firstOrFail();

        // Update
        $this->actingAs($admin)
            ->put(route('admin.lessons.update', $lesson), [
                'title'        => 'Lesson A (Updated)',
                'content_type' => 'text',
                'content_body' => 'Edited body.',
                'duration'     => 200,
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'Lesson A (Updated)']);

        // Delete
        $this->actingAs($admin)
            ->delete(route('admin.lessons.destroy', $lesson))
            ->assertRedirect();
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    // ---- Sessions CRUD ----

    public function test_admin_can_create_update_delete_session(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create();

        // Create
        $this->actingAs($admin)
            ->post(route('admin.sessions.store', $course), [
                'title'        => 'Live Class 1',
                'type'         => 'sync',
                'session_date' => now()->addWeek()->toDateString(),
                'start_time'   => '10:00',
                'end_time'     => '12:00',
                'meeting_link' => 'https://meet.example.com/abc',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('course_sessions', ['course_id' => $course->id, 'title' => 'Live Class 1']);
        $session = Session::where('title', 'Live Class 1')->firstOrFail();

        // Update
        $this->actingAs($admin)
            ->put(route('admin.sessions.update', $session), [
                'title'          => 'Live Class 1 (Updated)',
                'session_date'   => now()->addWeeks(2)->toDateString(),
                'start_time'     => '14:00',
                'end_time'       => '16:00',
                'status'         => 'completed',
                'recording_link' => 'https://videos.example.com/rec',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('course_sessions', ['id' => $session->id, 'title' => 'Live Class 1 (Updated)', 'status' => 'completed']);

        // Delete
        $this->actingAs($admin)
            ->delete(route('admin.sessions.destroy', $session))
            ->assertRedirect();
        $this->assertDatabaseMissing('course_sessions', ['id' => $session->id]);
    }

    // ---- Users management ----

    public function test_admin_can_delete_other_user_but_not_self(): void
    {
        $admin = $this->admin();
        $victim = User::factory()->create();

        // Delete another user
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $victim))
            ->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $victim->id]);

        // Cannot delete self
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    // ---- Authorization: every admin route is gated ----

    public function test_non_admin_is_blocked_from_all_admin_routes(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        $this->actingAs($student);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.courses.index'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.payments.index'))->assertForbidden();
        $this->get(route('admin.sessions.index', $course))->assertForbidden();
        $this->post(route('admin.sections.store', $course), ['title' => 'x'])->assertForbidden();
    }
}
