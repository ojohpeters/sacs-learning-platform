<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'full_description',
        'price',
        'thumbnail_path',
        'is_published',
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
     * Get the Asynchronous (Self-Paced) price.
     */
    public function getAsyncPriceAttribute(): float
    {
        return max($this->price - 25000 + 4000, 0);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
