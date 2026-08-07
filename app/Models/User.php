<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'ADMIN';

    public const ROLE_ORGANIZER = 'ORGANIZER';

    public const ROLE_PLAYER = 'PLAYER';

    /**
     * Attributes allowed during controlled mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Attributes hidden from serialized output.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Check whether the user has one of the supplied platform roles.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function ledTeams(): HasMany
{
    return $this->hasMany(Team::class, 'leader_id');
}
public function teamMemberships(): HasMany
{
    return $this->hasMany(TeamMember::class);
}

public function teamMembershipRequests(): HasMany
{
    return $this->hasMany(TeamMembershipRequest::class);
}

public function sentTeamMembershipRequests(): HasMany
{
    return $this->hasMany(
        TeamMembershipRequest::class,
        'requested_by_id'
    );
}

public function respondedTeamMembershipRequests(): HasMany
{
    return $this->hasMany(
        TeamMembershipRequest::class,
        'responded_by_id'
    );
}
}