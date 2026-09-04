<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_id',
        'name',
        'logo',
        'preferred_game_id',
        'description',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'leader_id'
        );
    }

    public function preferredGame(): BelongsTo
    {
        return $this->belongsTo(
            Game::class,
            'preferred_game_id'
        );
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(
            TeamMember::class
        );
    }

    public function membershipRequests(): HasMany
    {
        return $this->hasMany(
            TeamMembershipRequest::class
        );
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(
            Registration::class
        );
    }
}