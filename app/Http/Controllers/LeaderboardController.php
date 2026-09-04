<?php

namespace App\Http\Controllers;

use App\Models\MatchResult;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $leaderboard = MatchResult::query()
            ->select('winner_team_id')
            ->selectRaw('COUNT(*) as wins')
            ->where(
                'status',
                MatchResult::STATUS_VERIFIED
            )
            ->whereNotNull('winner_team_id')
            ->groupBy('winner_team_id')
            ->orderByDesc('wins')
            ->with('winnerTeam')
            ->get();

        return view(
            'leaderboard.index',
            compact('leaderboard')
        );
    }
}