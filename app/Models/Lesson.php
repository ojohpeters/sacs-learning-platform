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
     * marked complete — a fraction of the stated duration, clamped to a
     * sensible floor and ceiling (see config/learning.php).
     */
    public function requiredSeconds(): int
    {
        $fraction = (float) config('learning.required_fraction', 0.5);
        $min = (int) config('learning.min_seconds_per_lesson', 30);
        $max = (int) config('learning.max_seconds_per_lesson', 600);

        $base = (int) round(($this->duration ?? 0) * $fraction);

        return max($min, min($base ?: $min, $max));
    }
}
