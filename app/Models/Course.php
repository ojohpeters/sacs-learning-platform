<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Quiz;
use App\Models\QuizAttempt;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'short_description', 'full_description',
        'price', 'async_price', 'lesson_min_minutes', 'thumbnail_path', 'is_published',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the In-Class price (base + registration).
     */
    public function getInclassPriceAttribute(): float
    {
        return $this->price + 4000;
    }

    /**
     * Get the Synchronous (Live Online) price.
     */
    public function getSyncPriceAttribute(): float
    {
        return max($this->price - 10000 + 4000, 0);
    }

    /**
     * Get the Asynchronous (Self-Paced) price. Reads the raw stored value
     * ($value), falling back to a default when it hasn't been set.
     */
    public function getAsyncPriceAttribute($value): float
    {
        return $value !== null ? (float) $value : 15000;
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function quizzes()
    {
        return Quiz::whereIn('section_id', $this->sections()->pluck('id'));
    }

    public function quizAttempts()
    {
        return QuizAttempt::whereIn('quiz_id', $this->quizzes()->pluck('id'));
    }

    public function finalExam()
    {
        return Quiz::whereIn('section_id', $this->sections()->pluck('id'))
            ->where('type', 'final_exam')
            ->first();
    }

    public function sectionQuizzes()
    {
        return Quiz::whereIn('section_id', $this->sections()->pluck('id'))
            ->where('type', 'section_quiz')
            ->get();
    }
}
