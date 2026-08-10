<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPricingPolicy
{
    // Only the organizer who owns the tournament (or an admin) may edit pricing.
    // Called directly in the controller — not registered globally — so it never
    // collides with Member 01's own TournamentPolicy on the same model.
    public function manage(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->organizer_id || $user->role === 'admin';
    }
}