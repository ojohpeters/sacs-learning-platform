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
     *   2. Video: the video's own length (its duration).
     *   3. Text: estimated reading time from the content's word count.
     *   4. Image / PDF: a fixed floor.
     */
    public function requiredSeconds(): int
    {
        if ($this->min_seconds !== null) {
            return max(0, (int) $this->min_seconds);
        }

        if ($this->content_type === 'video') {
            return max((int) config('learning.min_video_seconds', 10), (int) ($this->duration ?? 0));
        }

        if ($this->content_type === 'text') {
            $words = str_word_count(trim(strip_tags((string) $this->content_body)));
            $wpm = max(1, (int) config('learning.reading_wpm', 200));
            $seconds = (int) ceil($words / $wpm * 60);

            return max((int) config('learning.min_reading_seconds', 10), $seconds);
        }

        // image / pdf
        return (int) config('learning.min_other_seconds', 20);
    }
}
