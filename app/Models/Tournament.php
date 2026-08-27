<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_REGISTRATION_OPEN = 'REGISTRATION_OPEN';

    public const STATUS_REGISTRATION_CLOSED = 'REGISTRATION_CLOSED';

    public const STATUS_ONGOING = 'ONGOING';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    public const PHASE_UPCOMING = 'UPCOMING';

    public const PHASE_ONGOING = 'ONGOING';

    public const PHASE_COMPLETED = 'COMPLETED';

    /**
     * Fields allowed for mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organizer_id',
        'game_id',
        'category',
        'region',
        'prize_type',
        'title',
        'description',
        'registration_deadline',
        'start_at',
        'end_at',
        'rules',
        'prize_pool',
        'team_limit',
        'match_format',
        'status',
    ];

    /**
     * Convert database values into appropriate PHP types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registration_deadline' => 'datetime',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'prize_pool' => 'decimal:2',
            'team_limit' => 'integer',
        ];
    }

    /**
     * Return all supported tournament lifecycle statuses.
     *
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_REGISTRATION_OPEN,
            self::STATUS_REGISTRATION_CLOSED,
            self::STATUS_ONGOING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Return the valid next statuses for the current tournament state.
     *
     * @return list<string>
     */
    public function allowedStatusTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_DRAFT => [
                self::STATUS_REGISTRATION_OPEN,
                self::STATUS_CANCELLED,
            ],

            self::STATUS_REGISTRATION_OPEN => [
                self::STATUS_REGISTRATION_CLOSED,
                self::STATUS_CANCELLED,
            ],

            self::STATUS_REGISTRATION_CLOSED => [
                self::STATUS_ONGOING,
                self::STATUS_CANCELLED,
            ],

            self::STATUS_ONGOING => [
                self::STATUS_COMPLETED,
                self::STATUS_CANCELLED,
            ],

            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED => [],

            default => [],
        };
    }

    /**
     * Determine whether the tournament may move to the supplied status.
     */
    public function canTransitionTo(string $status): bool
    {
        return in_array(
            $status,
            $this->allowedStatusTransitions(),
            true
        );
    }

    /**
     * Move the tournament to a valid next lifecycle status.
     */
    public function transitionTo(string $status): bool
    {
        if (! $this->canTransitionTo($status)) {
            return false;
        }

        return $this->update([
            'status' => $status,
        ]);
    }

    /**
     * Determine the date-based tournament phase used for display.
     */
    public function timelinePhase(): string
    {
        $now = now();

        if ($now->lt($this->start_at)) {
            return self::PHASE_UPCOMING;
        }

        if ($now->lte($this->end_at)) {
            return self::PHASE_ONGOING;
        }

        return self::PHASE_COMPLETED;
    }

    /**
     * The organizer who owns the tournament.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'organizer_id'
        );
    }

    /**
     * The game selected for the tournament.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Team registrations submitted for this tournament.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Scheduled matches for this tournament.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }
}   