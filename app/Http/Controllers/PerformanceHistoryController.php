<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Services\PerformanceHistoryService;

class PerformanceHistoryController extends Controller
{
    public function __construct(
        private PerformanceHistoryService $performanceHistoryService
    ) {
    }

    /**
     * Show performance history for one team.
     */
    public function team(Team $team)
    {
        $performance = $this->performanceHistoryService
            ->teamPerformance($team);

        return view('performance.team', [
            'performance' => $performance,
        ]);
    }

    /**
     * Show performance history for one player.
     */
    public function player(User $user)
    {
        abort_unless(
            $user->role === User::ROLE_PLAYER,
            404
        );

        $performance = $this->performanceHistoryService
            ->playerPerformance($user);

        return view('performance.player', [
            'performance' => $performance,
        ]);
    }
}