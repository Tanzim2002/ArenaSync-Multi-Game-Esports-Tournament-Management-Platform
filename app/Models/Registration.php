<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * Attributes that can be mass assigned.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tournament_id',
        'team_id',
        'status',
        'submitted_at',
    ];

    /**
     * Attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Tournament this registration belongs to.
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Team that submitted this registration.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
    /**
     * The payment submitted for this registration, if any.
     */
    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }
    /**
     * Determine whether the registration is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Determine whether the registration is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Determine whether the registration is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Determine whether the registration is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Determine whether an organizer may still review this registration.
     */
    public function isReviewable(): bool
    {
        return $this->isPending();
    }

    /**
     * Approve a pending registration.
     */
    public function approve(): bool
    {
        return $this->makeDecision(
            self::STATUS_APPROVED
        );
    }

    /**
     * Reject a pending registration.
     */
    public function reject(): bool
    {
        return $this->makeDecision(
            self::STATUS_REJECTED
        );
    }

    /**
     * Apply a single approval decision to a pending registration.
     */
    private function makeDecision(string $status): bool
    {
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
            ->whereKey($this->getKey())
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