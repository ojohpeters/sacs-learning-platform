<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'learning_type',
        'status',
        'enrolled_at',
        'completed_at',
        'certificate_code',
        'certificate_issued_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'certificate_issued_at' => 'datetime',
        ];
    }

    /**
     * Ensure this enrollment has a certificate code, generating one on first
     * issue. Returns the (existing or new) code.
     */
    public function issueCertificate(): string
    {
        if (! $this->certificate_code) {
            $this->forceFill([
                'certificate_code' => 'SACS-'.strtoupper(Str::random(10)),
                'certificate_issued_at' => $this->certificate_issued_at ?? now(),
            ])->save();
        }

        return $this->certificate_code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
