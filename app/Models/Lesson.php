<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\LessonCompletion;


class Lesson extends Model
{
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
        if (!$user) return false;
        return $this->completions()->where('user_id', $user->id)->exists();
    }
}
