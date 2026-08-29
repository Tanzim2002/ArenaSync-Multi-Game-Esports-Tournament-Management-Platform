<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    public const STATUS_SCHEDULED = 'SCHEDULED';

    public const STATUS_ONGOING = 'ONGOING';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * Fields allowed for mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tournament_id',
        'round',
        'team_one_id',
        'team_two_id',
        'scheduled_at',
        'status',
        'created_by',
    ];

    /**
     * Return all supported match statuses.
     *
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_ONGOING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Return the valid next statuses for the current match state.
     *
     * @return list<string>
     */
    public function allowedStatusTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => [self::STATUS_ONGOING, self::STATUS_CANCELLED],
            self::STATUS_ONGOING => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_COMPLETED, self::STATUS_CANCELLED => [],
            default => [],
        };
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->allowedStatusTransitions(), true);
    }

    public function transitionTo(string $status): bool
    {
        if (! $this->canTransitionTo($status)) {
            return false;
        }

        return $this->update(['status' => $status]);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teamOne(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_one_id');
    }

    public function teamTwo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_two_id');
    }
    
    public function result(): HasOne
{
    return $this->hasOne(MatchResult::class, 'match_id');
}

    /**
     * Convert database values into appropriate PHP types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }
}
