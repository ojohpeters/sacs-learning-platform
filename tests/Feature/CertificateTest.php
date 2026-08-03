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

    /**
     * Complete every lesson in the course for the given user + enrollment.
     */
    private function completeAll($user, $enrollment, $lessons): void
    {
        foreach ($lessons as $lesson) {
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'enrollment_id' => $enrollment->id,
                'completed_at' => now(),
            ]);
        }
    }

    public function test_viewing_certificate_issues_a_stable_code(): void
    {
        [$course, , $lessons] = $this->courseWithLessons(2);
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
        $this->completeAll($user, $enrollment, $lessons);

        $this->actingAs($user)->get(route('certificate.show', $course->slug))->assertOk();

        $code = $enrollment->fresh()->certificate_code;
        $this->assertNotNull($code);
        $this->assertStringStartsWith('SACS-', $code);

        // Viewing again must not regenerate the code.
        $this->actingAs($user)->get(route('certificate.show', $course->slug))->assertOk();
        $this->assertSame($code, $enrollment->fresh()->certificate_code);
    }

    public function test_certificate_shows_verification_link(): void
    {
        [$course, , $lessons] = $this->courseWithLessons(1);
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
        $this->completeAll($user, $enrollment, $lessons);

        $this->actingAs($user)
            ->get(route('certificate.show', $course->slug))
            ->assertOk()
            ->assertSee(route('certificate.verify', $enrollment->fresh()->certificate_code));
    }

    public function test_public_can_verify_a_valid_certificate(): void
    {
        [$course, , $lessons] = $this->courseWithLessons(1);
        $user = User::factory()->create(['name' => 'Grace Hopper']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
        $this->completeAll($user, $enrollment, $lessons);
        $code = $enrollment->issueCertificate();

        // Verification is public — no auth.
        $this->get(route('certificate.verify', $code))
            ->assertOk()
            ->assertViewIs('certificate.verify')
            ->assertSee('Grace Hopper')
            ->assertSee($course->title)
            ->assertSee('verified', false);
    }

    public function test_unknown_certificate_code_shows_not_found(): void
    {
        $this->get(route('certificate.verify', 'SACS-DOESNOTEXIST'))
            ->assertOk()
            ->assertSee('not found', false);
    }
}
