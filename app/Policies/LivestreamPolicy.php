<?php

namespace App\Policies;

use App\Models\Livestream;
use App\Models\Tournament;
use App\Models\User;

class LivestreamPolicy
{
    public function manage(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->organizer_id || $user->role === 'admin';
    }

    public function delete(User $user, Livestream $livestream): bool
    {
        return $this->manage($user, $livestream->tournament);
    }
}