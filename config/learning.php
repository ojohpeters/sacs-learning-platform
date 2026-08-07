<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Minimum time on a lesson
    |--------------------------------------------------------------------------
    |
    | A lesson can only be marked complete after the student has accumulated
    | enough *active* time on it (tracked via heartbeats). The requirement is a
    | fraction of the lesson's stated duration, clamped between a floor and a
    | ceiling so trivially short or very long lessons stay reasonable.
    |
    */

    'required_fraction' => (float) env('LEARNING_REQUIRED_FRACTION', 0.5),

    'min_seconds_per_lesson' => (int) env('LEARNING_MIN_SECONDS', 30),

    'max_seconds_per_lesson' => (int) env('LEARNING_MAX_SECONDS', 600),

    // How often the player reports active time, in seconds.
    'heartbeat_interval' => (int) env('LEARNING_HEARTBEAT_INTERVAL', 15),

];
