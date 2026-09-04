<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_VERIFIED = 'VERIFIED';

    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'registration_id',
        'amount',
        'method',
        'reference',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_VERIFIED,
            self::STATUS_REJECTED,
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(
            Registration::class
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function isPending(): bool
    {
        return $this->status
            === self::STATUS_PENDING;
    }

    public function isVerified(): bool
    {
        return $this->status
            === self::STATUS_VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status
            === self::STATUS_REJECTED;
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',

            'submitted_at' => 'datetime',

            'reviewed_at' => 'datetime',
        ];
    }
}