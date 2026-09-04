<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'tournament_id',
        'team_id',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(
            Tournament::class
        );
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(
            Team::class
        );
    }

    public function payment(): HasOne
    {
        return $this->hasOne(
            Payment::class
        );
    }

    public function isPending(): bool
    {
        return $this->status
            === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status
            === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status
            === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status
            === self::STATUS_CANCELLED;
    }

    public function isReviewable(): bool
    {
        return $this->isPending();
    }

    public function approve(): bool
    {
        return $this->makeDecision(
            self::STATUS_APPROVED
        );
    }

    public function reject(): bool
    {
        return $this->makeDecision(
            self::STATUS_REJECTED
        );
    }

    private function makeDecision(
        string $status
    ): bool {
        if (
            ! in_array(
                $status,
                [
                    self::STATUS_APPROVED,
                    self::STATUS_REJECTED,
                ],
                true
            )
        ) {
            return false;
        }

        $updated = self::query()
            ->whereKey(
                $this->getKey()
            )
            ->where(
                'status',
                self::STATUS_PENDING
            )
            ->update([
                'status' => $status,
            ]);

        if ($updated !== 1) {
            return false;
        }

        $this->status = $status;

        return true;
    }
}