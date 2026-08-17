<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'type',
        'description',
        'passing_score',
        'time_limit',
        'min_submit_time',
        'show_correct_answers',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'passing_score' => 'integer',
            'time_limit' => 'integer',
            'min_submit_time' => 'integer',
            'show_correct_answers' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function passedByUser(?User $user): bool
    {
        if (!$user) return false;
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'passed')
            ->exists();
    }

    public function latestAttemptByUser(?User $user): ?QuizAttempt
    {
        if (!$user) return null;
        return $this->attempts()
            ->where('user_id', $user->id)
            ->latest()
            ->first();
    }
}