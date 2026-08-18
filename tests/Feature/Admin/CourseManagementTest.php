<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_is_forbidden_from_admin(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_create_course(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.courses.store'), [
                'title' => 'Intro to Testing',
                'short_description' => 'A short description.',
                'full_description' => 'A much longer description of the course.',
                'price' => 50000,
                'async_price' => 15000,
                'lesson_min_minutes' => 1,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'Intro to Testing',
            'slug' => 'intro-to-testing',
        ]);
    }

    public function test_duplicate_titles_produce_unique_slugs(): void
    {
        $admin = User::factory()->admin()->create();

        // A course with the target slug already exists.
        Course::factory()->create(['title' => 'Duplicate', 'slug' => 'duplicate']);

        $this->actingAs($admin)
            ->post(route('admin.courses.store'), [
                'title' => 'Duplicate',
                'short_description' => 'Short.',
                'full_description' => 'Longer description.',
                'price' => 10000,
                'async_price' => 15000,
                'lesson_min_minutes' => 1,
            ])
            ->assertRedirect(route('admin.courses.index'));

        // Previously the unique constraint on slug caused a 500; now it appends -2.
        $this->assertDatabaseHas('courses', ['slug' => 'duplicate-2']);
    }

    public function test_admin_can_delete_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course->id))
            ->assertRedirect(route('admin.courses.index'));

        // Soft-deleted: gone from default queries but the row is retained.
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_deleting_course_preserves_payment_history(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $payment = Payment::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course->id))
            ->assertRedirect(route('admin.courses.index'));

        // The course is soft-deleted; its financial record survives.
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_course_validation_rejects_missing_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.courses.store'), [])
            ->assertSessionHasErrors(['title', 'short_description', 'full_description', 'price']);
    }
}
