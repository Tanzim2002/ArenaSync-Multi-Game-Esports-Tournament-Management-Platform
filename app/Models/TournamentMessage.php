<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMessage extends Model
{
    use HasFactory;

    public const TYPE_CHAT = 'CHAT';
    public const TYPE_ANNOUNCEMENT = 'ANNOUNCEMENT';

    protected $fillable = [
        'tournament_id',
        'user_id',
        'type',
        'message',
    ];

    /**
     * Tournament this message belongs to.
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * User who posted this message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine whether this record is an announcement.
     */
    public function isAnnouncement(): bool
    {
        return $this->type === self::TYPE_ANNOUNCEMENT;
    }
}
