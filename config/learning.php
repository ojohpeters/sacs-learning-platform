<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Minimum time on a lesson
    |--------------------------------------------------------------------------
    |
    | A lesson can only be marked complete after the student has accumulated
    | enough *active* time on it (tracked via heartbeats). The requirement is
    | derived per content type:
    |
    |   video        → the video's own length (its duration in seconds)
    |   text         → estimated reading time from the content length
    |   image / pdf  → a fixed floor
    |
    | A per-lesson `min_seconds` override always wins (0 = no time gate).
    |
    */

    // How often the player reports active time, in seconds.
    'heartbeat_interval' => (int) env('LEARNING_HEARTBEAT_INTERVAL', 15),

    // Video lessons: minimum viewing time = the video's length. This floor only
    // applies when the lesson's duration is unknown/zero.
    'min_video_seconds' => (int) env('LEARNING_MIN_VIDEO_SECONDS', 10),

    // Text lessons: reading speed used to estimate time from word count, plus a
    // floor for very short pages.
    'reading_wpm' => (int) env('LEARNING_READING_WPM', 200),
    'min_reading_seconds' => (int) env('LEARNING_MIN_READING_SECONDS', 10),

    // Image / PDF lessons: a fixed minimum.
    'min_other_seconds' => (int) env('LEARNING_MIN_OTHER_SECONDS', 20),

];
