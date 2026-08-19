<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
     * Fields allowed for mass assignment.
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
     * Fields hidden during serialization.
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
     * Tournaments owned by this organizer.
     */
    public function organizedTournaments(): HasMany
    {
        return $this->hasMany(
            Tournament::class,
            'organizer_id'
        );
    }

    /**
     * Organizer verification record owned by this user.
     */
    public function organizerVerification(): HasOne
    {
        return $this->hasOne(
            OrganizerVerification::class,
            'organizer_id'
        );
    }

    /**
     * Verification requests reviewed by this administrator.
     */
    public function reviewedOrganizerVerifications(): HasMany
    {
        return $this->hasMany(
            OrganizerVerification::class,
            'reviewed_by'
        );
    }

    /**
     * Teams led by this user.
     */
    public function ledTeams(): HasMany
    {
        return $this->hasMany(
            Team::class,
            'leader_id'
        );
    }

    /**
     * Team memberships owned by this user.
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(
            TeamMember::class
        );
    }

    /**
     * Team membership requests associated with this user.
     */
    public function teamMembershipRequests(): HasMany
    {
        return $this->hasMany(
            TeamMembershipRequest::class
        );
    }

    /**
     * Team membership requests initiated by this user.
     */
    public function sentTeamMembershipRequests(): HasMany
    {
        return $this->hasMany(
            TeamMembershipRequest::class,
            'requested_by_id'
        );
    }

    /**
     * Team membership requests reviewed by this user.
     */
    public function respondedTeamMembershipRequests(): HasMany
    {
        return $this->hasMany(
            TeamMembershipRequest::class,
            'responded_by_id'
        );
    }

    /**
     * Convert database values into appropriate PHP types.
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
}
