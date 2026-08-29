<?php

namespace App\Http\Controllers;

use App\Models\MatchResult;
use App\Models\Tournament;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaderboard = MatchResult::select(
                'winner_team_id'
            )
            ->selectRaw('COUNT(*) as wins')
            ->where('status', MatchResult::STATUS_VERIFIED)
            ->groupBy('winner_team_id')
            ->orderByDesc('wins')
            ->with('winnerTeam')
            ->get();

        return view('leaderboard.index', compact('leaderboard'));
    }
}
