<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonContentTest extends TestCase
{
    use RefreshDatabase;

    private function lessonWithFile(bool $preview = false): array
    {
        Storage::fake('local');
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $path = 'course-content/lesson.mp4';
        Storage::disk('local')->put($path, 'fake-video-bytes');

        $lesson = Lesson::factory()->create([
            'section_id' => $section->id,
            'content_type' => 'video',
            'content_path' => $path,
            'is_free_preview' => $preview,
        ]);

        return [$course, $lesson];
    }

    public function test_enrolled_user_can_stream_protected_content(): void
    {
        [$course, $lesson] = $this->lessonWithFile();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('lesson.content', [$course->slug, $lesson->id]))
            ->assertOk();
    }

    public function test_non_enrolled_user_cannot_stream_protected_content(): void
    {
        [$course, $lesson] = $this->lessonWithFile();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('lesson.content', [$course->slug, $lesson->id]))
            ->assertForbidden();
    }

    public function test_guest_cannot_stream_protected_content(): void
    {
        [$course, $lesson] = $this->lessonWithFile();

        $this->get(route('lesson.content', [$course->slug, $lesson->id]))
            ->assertForbidden();
    }

    public function test_anyone_can_stream_free_preview_content(): void
    {
        [$course, $lesson] = $this->lessonWithFile(preview: true);

        $this->get(route('lesson.content', [$course->slug, $lesson->id]))
            ->assertOk();
    }

    public function test_content_for_lesson_in_another_course_is_404(): void
    {
        [$course, $lesson] = $this->lessonWithFile(preview: true);
        $otherCourse = Course::factory()->create();

        $this->get(route('lesson.content', [$otherCourse->slug, $lesson->id]))
            ->assertNotFound();
    }

    public function test_external_url_content_redirects_away(): void
    {
        $course = Course::factory()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->preview()->create([
            'section_id' => $section->id,
            'content_type' => 'video',
            'content_path' => 'https://example.com/video.mp4',
        ]);

        $this->get(route('lesson.content', [$course->slug, $lesson->id]))
            ->assertRedirect('https://example.com/video.mp4');
    }
}
