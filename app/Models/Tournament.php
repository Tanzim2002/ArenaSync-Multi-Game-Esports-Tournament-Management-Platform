<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tournament extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_REGISTRATION_OPEN = 'REGISTRATION_OPEN';

    public const STATUS_REGISTRATION_CLOSED = 'REGISTRATION_CLOSED';

    public const STATUS_ONGOING = 'ONGOING';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * Fields allowed for mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organizer_id',
        'game_id',
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
     * The organizer who owns the tournament.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * The game selected for the tournament.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}