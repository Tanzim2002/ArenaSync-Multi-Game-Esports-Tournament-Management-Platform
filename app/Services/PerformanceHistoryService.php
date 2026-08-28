<?php

namespace App\Services;

use App\Models\MatchResult;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class PerformanceHistoryService
{
    /**
     * Build performance history for one team.
     *
     * Only VERIFIED match results are counted.
     */
    public function teamPerformance(Team $team): array
    {
        $results = MatchResult::query()
            ->where('status', MatchResult::STATUS_VERIFIED)
            ->whereHas('match', function ($query) use ($team) {
                $query->where(function ($matchQuery) use ($team) {
                    $matchQuery
                        ->where('team_one_id', $team->id)
                        ->orWhere('team_two_id', $team->id);
                });
            })
            ->with([
                'match.tournament',
                'match.teamOne',
                'match.teamTwo',
                'winnerTeam',
            ])
            ->get()
            ->sortByDesc(function (MatchResult $result) {
                return $result->match?->scheduled_at?->timestamp ?? 0;
            })
            ->values();

        $matchesPlayed = 0;
        $wins = 0;
        $losses = 0;
        $draws = 0;
        $scoreFor = 0;
        $scoreAgainst = 0;
        $history = [];

        foreach ($results as $result) {
            $match = $result->match;

            if (! $match) {
                continue;
            }

            $isTeamOne = (int) $match->team_one_id === (int) $team->id;

            $teamScore = $isTeamOne
                ? (int) $result->team_one_score
                : (int) $result->team_two_score;

            $opponentScore = $isTeamOne
                ? (int) $result->team_two_score
                : (int) $result->team_one_score;

            $opponent = $isTeamOne
                ? $match->teamTwo
                : $match->teamOne;

            $outcome = 'DRAW';

            if ($result->winner_team_id !== null) {
                if ((int) $result->winner_team_id === (int) $team->id) {
                    $outcome = 'WIN';
                    $wins++;
                } else {
                    $outcome = 'LOSS';
                    $losses++;
                }
            } else {
                $draws++;
            }

            $matchesPlayed++;
            $scoreFor += $teamScore;
            $scoreAgainst += $opponentScore;

            $history[] = [
                'match_id' => $match->id,
                'tournament' => $match->tournament?->title ?? 'Unknown Tournament',
                'round' => $match->round,
                'opponent' => $opponent?->name ?? 'Unknown Opponent',
                'team_score' => $teamScore,
                'opponent_score' => $opponentScore,
                'outcome' => $outcome,
                'scheduled_at' => $match->scheduled_at,
            ];
        }

        $winRate = $matchesPlayed > 0
            ? round(($wins / $matchesPlayed) * 100, 2)
            : 0;

        return [
            'team' => $team,
            'summary' => [
                'matches_played' => $matchesPlayed,
                'wins' => $wins,
                'losses' => $losses,
                'draws' => $draws,
                'score_for' => $scoreFor,
                'score_against' => $scoreAgainst,
                'win_rate' => $winRate,
            ],
            'history' => $history,
        ];
    }

    /**
     * Build player performance from the teams the player
     * currently leads or belongs to.
     *
     * The project stores team-level match results, so this
     * does not invent individual kills, goals, assists, etc.
     */
    public function playerPerformance(User $user): array
    {
        $user->loadMissing([
            'ledTeams',
            'teamMemberships.team',
        ]);

        $teams = $this->playerTeams($user);

        $teamPerformances = $teams
            ->map(function (Team $team) {
                return $this->teamPerformance($team);
            })
            ->values();

        $matchesPlayed = $teamPerformances
            ->sum(fn (array $performance) => $performance['summary']['matches_played']);

        $wins = $teamPerformances
            ->sum(fn (array $performance) => $performance['summary']['wins']);

        $losses = $teamPerformances
            ->sum(fn (array $performance) => $performance['summary']['losses']);

        $draws = $teamPerformances
            ->sum(fn (array $performance) => $performance['summary']['draws']);

        $scoreFor = $teamPerformances
            ->sum(fn (array $performance) => $performance['summary']['score_for']);

        $scoreAgainst = $teamPerformances
            ->sum(fn (array $performance) => $performance['summary']['score_against']);

        $winRate = $matchesPlayed > 0
            ? round(($wins / $matchesPlayed) * 100, 2)
            : 0;

        return [
            'player' => $user,
            'teams' => $teams,
            'summary' => [
                'matches_played' => $matchesPlayed,
                'wins' => $wins,
                'losses' => $losses,
                'draws' => $draws,
                'score_for' => $scoreFor,
                'score_against' => $scoreAgainst,
                'win_rate' => $winRate,
            ],
            'team_performances' => $teamPerformances,
        ];
    }

    /**
     * Return unique teams currently connected to a player.
     */
    private function playerTeams(User $user): Collection
    {
        $ledTeams = $user->ledTeams;

        $memberTeams = $user->teamMemberships
            ->pluck('team')
            ->filter();

        return $ledTeams
            ->concat($memberTeams)
            ->unique('id')
            ->values();
    }
}