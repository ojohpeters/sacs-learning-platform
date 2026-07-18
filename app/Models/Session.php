<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    protected $table = 'course_sessions';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'session_date',
        'start_time',
        'end_time',
        'meeting_link',
        'recording_link',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' && $this->session_date >= now()->toDateString();
    }

    public function isPast(): bool
    {
        return $this->status === 'completed' || $this->session_date < now()->toDateString();
    }
}