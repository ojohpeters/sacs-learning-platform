<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Quiz;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function quiz(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quiz::class);
    }
}
