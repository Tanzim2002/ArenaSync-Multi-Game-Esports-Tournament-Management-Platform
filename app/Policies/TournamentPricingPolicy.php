<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPricingPolicy
{
    public function manage(
        ?User $user,
        Tournament $tournament
    ): bool {
        if ($user === null) {
            return false;
        }

        if (
            $user->hasRole(
                User::ROLE_ADMIN
            )
        ) {
            return true;
        }

        return $user->hasRole(
            User::ROLE_ORGANIZER
        )
            &&
            (int) $user->id
                ===
            (int) $tournament->organizer_id;
    }
}