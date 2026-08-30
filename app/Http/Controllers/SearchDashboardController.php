<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchDashboardController extends Controller
{
    /**
     * Display the role-based dashboard and search/filter results.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $search = trim((string) $request->query('search', ''));
        $type = $request->query('type', 'all');
        $gameId = $request->query('game_id');
        $status = $request->query('status');
        $region = $request->query('region');

        /*
        |--------------------------------------------------------------------------
        | Games
        |--------------------------------------------------------------------------
        */

        $gamesQuery = Game::query()
            ->withCount('tournaments')
            ->orderBy('name');

        if ($search !== '') {
            $gamesQuery->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        $games = in_array($type, ['all', 'games'], true)
            ? $gamesQuery->limit(10)->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Teams
        |--------------------------------------------------------------------------
        */

        $teamsQuery = Team::query()
            ->with('leader')
            ->withCount('teamMembers')
            ->orderBy('name');

        if ($search !== '') {
            $teamsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas(
                        'leader',
                        fn ($leaderQuery) =>
                            $leaderQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                    );
            });
        }

        if ($gameId) {
            $teamsQuery->where(
                'preferred_game_id',
                $gameId
            );
        }

        $teams = in_array($type, ['all', 'teams'], true)
            ? $teamsQuery->limit(10)->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Players
        |--------------------------------------------------------------------------
        */

        $playersQuery = User::query()
            ->where('role', User::ROLE_PLAYER)
            ->orderBy('name');

        if ($search !== '') {
            $playersQuery->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $players = in_array($type, ['all', 'players'], true)
            ? $playersQuery->limit(10)->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Tournaments
        |--------------------------------------------------------------------------
        */

        $tournamentsQuery = Tournament::query()
            ->with([
                'game',
                'organizer',
            ])
            ->where(
                'status',
                '!=',
                Tournament::STATUS_DRAFT
            )
            ->orderBy('start_at');

        if ($search !== '') {
            $tournamentsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhereHas(
                        'game',
                        fn ($gameQuery) =>
                            $gameQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                    );
            });
        }

        if ($gameId) {
            $tournamentsQuery->where(
                'game_id',
                $gameId
            );
        }

        if ($status) {
            $tournamentsQuery->where(
                'status',
                $status
            );
        }

        if ($region) {
            $tournamentsQuery->where(
                'region',
                $region
            );
        }

        $tournaments = in_array(
            $type,
            ['all', 'tournaments'],
            true
        )
            ? $tournamentsQuery->limit(10)->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Role-Based Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $dashboard = [];

        if ($user->hasRole(User::ROLE_PLAYER)) {
            $teamIds = $user->ledTeams()
                ->pluck('id')
                ->merge(
                    $user->teamMemberships()
                        ->pluck('team_id')
                )
                ->unique();

            $dashboard = [
                'teams_led' =>
                    $user->ledTeams()->count(),

                'team_memberships' =>
                    $user->teamMemberships()->count(),

                'registrations' =>
                    Registration::query()
                        ->whereIn(
                            'team_id',
                            $teamIds
                        )
                        ->count(),

                'upcoming_matches' =>
                    GameMatch::query()
                        ->where(
                            'status',
                            GameMatch::STATUS_SCHEDULED
                        )
                        ->where(function ($query) use ($teamIds): void {
                            $query
                                ->whereIn(
                                    'team_one_id',
                                    $teamIds
                                )
                                ->orWhereIn(
                                    'team_two_id',
                                    $teamIds
                                );
                        })
                        ->count(),
            ];
        }

        if ($user->hasRole(User::ROLE_ORGANIZER)) {
            $dashboard = [
                'tournaments' =>
                    $user->organizedTournaments()->count(),

                'open_tournaments' =>
                    $user->organizedTournaments()
                        ->where(
                            'status',
                            Tournament::STATUS_REGISTRATION_OPEN
                        )
                        ->count(),

                'pending_registrations' =>
                    Registration::query()
                        ->where(
                            'status',
                            Registration::STATUS_PENDING
                        )
                        ->whereHas(
                            'tournament',
                            fn ($query) =>
                                $query->where(
                                    'organizer_id',
                                    $user->id
                                )
                        )
                        ->count(),

                'scheduled_matches' =>
                    GameMatch::query()
                        ->where(
                            'status',
                            GameMatch::STATUS_SCHEDULED
                        )
                        ->whereHas(
                            'tournament',
                            fn ($query) =>
                                $query->where(
                                    'organizer_id',
                                    $user->id
                                )
                        )
                        ->count(),
            ];
        }

        if ($user->hasRole(User::ROLE_ADMIN)) {
            $dashboard = [
                'users' =>
                    User::query()->count(),

                'games' =>
                    Game::query()->count(),

                'teams' =>
                    Team::query()->count(),

                'tournaments' =>
                    Tournament::query()->count(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        */

        $filterGames = Game::query()
            ->orderBy('name')
            ->get();

        $regions = Tournament::query()
            ->whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        return view(
            'dashboard.index',
            compact(
                'user',
                'dashboard',
                'games',
                'teams',
                'players',
                'tournaments',
                'filterGames',
                'regions',
                'search',
                'type',
                'gameId',
                'status',
                'region'
            )
        );
    }
}