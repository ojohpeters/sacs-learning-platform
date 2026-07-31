<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSessionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSession(Course $course, array $overrides = []): Session
    {
        return Session::create(array_merge([
            'course_id' => $course->id,
            'title' => 'Live Class',
            'type' => 'sync',
            'session_date' => now()->addWeek()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'upcoming',
        ], $overrides));
    }

    public function test_guest_cannot_view_live_sessions(): void
    {
        $course = Course::factory()->create();

        $this->get(route('learning.sessions', $course->slug))
            ->assertRedirect(route('login'));
    }

    public function test_non_enrolled_user_is_redirected(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('learning.sessions', $course->slug))
            ->assertRedirect(route('student.courses'))
            ->assertSessionHas('error');
    }

    public function test_enrolled_user_sees_upcoming_and_past_sessions(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'learning_type' => 'sync',
        ]);

        $upcoming = $this->makeSession($course, [
            'title' => 'Kickoff Webinar',
            'session_date' => now()->addDays(3)->toDateString(),
            'meeting_link' => 'https://meet.example.com/kickoff',
        ]);
        $past = $this->makeSession($course, [
            'title' => 'Orientation Recap',
            'session_date' => now()->subDays(3)->toDateString(),
            'status' => 'completed',
            'recording_link' => 'https://videos.example.com/orientation',
        ]);

        $this->actingAs($user)
            ->get(route('learning.sessions', $course->slug))
            ->assertOk()
            ->assertViewIs('learning.sessions')
            ->assertSee('Kickoff Webinar')
            ->assertSee('Orientation Recap');
    }
}
