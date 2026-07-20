<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: Course, 1: Section, 2: Collection<int, Lesson>}
     */
    private function courseWithLessons(int $count = 2): array
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count($count)->create(['section_id' => $section->id]);

        return [$course, $section, $lessons];
    }

    public function test_non_enrolled_user_is_redirected(): void
    {
        [$course] = $this->courseWithLessons();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('certificate.show', $course->slug))
            ->assertRedirect(route('student.courses'))
            ->assertSessionHas('error');
    }

    public function test_enrolled_user_with_incomplete_course_is_redirected(): void
    {
        [$course, , $lessons] = $this->courseWithLessons(2);
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Complete only one of the two lessons.
        LessonCompletion::create([
            'user_id' => $user->id,
            'lesson_id' => $lessons->first()->id,
            'enrollment_id' => $enrollment->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('certificate.show', $course->slug))
            ->assertRedirect(route('learning.course', $course->slug))
            ->assertSessionHas('error');
    }

    public function test_certificate_unlocks_when_all_lessons_complete(): void
    {
        [$course, , $lessons] = $this->courseWithLessons(2);
        $user = User::factory()->create(['name' => 'Ada Lovelace']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        foreach ($lessons as $lesson) {
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'enrollment_id' => $enrollment->id,
                'completed_at' => now(),
            ]);
        }

        $this->actingAs($user)
            ->get(route('certificate.show', $course->slug))
            ->assertOk()
            ->assertViewIs('certificate.show')
            ->assertSee('Ada Lovelace')
            ->assertSee($course->title);
    }

    public function test_guest_cannot_view_certificate(): void
    {
        [$course] = $this->courseWithLessons();

        $this->get(route('certificate.show', $course->slug))
            ->assertRedirect(route('login'));
    }
}
