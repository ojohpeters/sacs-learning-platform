<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'enrollment_id',
        'payment_gateway',
        'transaction_reference',
        'amount',
        'currency',
        'status',
        'learning_type',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}