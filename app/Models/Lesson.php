<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'content_type',
        'content_path',
        'content_body',
        'duration',
        'min_seconds',
        'order',
        'is_free_preview',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function isCompletedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->completions()->where('user_id', $user->id)->exists();
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Active seconds a student must accumulate before this lesson can be
     * marked complete.
     *
     * Precedence:
     *   1. Per-lesson `min_seconds` override (0 disables the gate entirely).
     *   2. Video lessons: a fraction of the runtime, clamped to a floor/ceiling.
     *   3. Reading pages (text/image/pdf): a short fixed floor.
     */
    public function requiredSeconds(): int
    {
        if ($this->min_seconds !== null) {
            return max(0, (int) $this->min_seconds);
        }

        $max = (int) config('learning.max_seconds_per_lesson', 600);

        if ($this->content_type !== 'video') {
            return min((int) config('learning.reading_seconds', 20), $max);
        }

        $fraction = (float) config('learning.required_fraction', 0.5);
        $floor = (int) config('learning.min_seconds_per_lesson', 30);
        $base = (int) round(($this->duration ?? 0) * $fraction);

        return max($floor, min($base ?: $floor, $max));
    }
}
