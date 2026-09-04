<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Game;
use App\Models\Team;
use App\Models\TeamMembershipRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function create(): View
    {
        $games = Game::query()
            ->orderBy('name')
            ->get();

        return view(
            'teams.create',
            compact('games')
        );
    }

    public function show(Team $team): View
    {
        $team->load([
            'leader',
            'preferredGame',
            'teamMembers.user',
        ]);

        $pendingRequestsQuery =
            TeamMembershipRequest::query()
                ->where(
                    'team_id',
                    $team->id
                )
                ->where(
                    'status',
                    TeamMembershipRequest::STATUS_PENDING
                )
                ->with([
                    'user',
                    'requestedBy',
                ]);

        if (
            (int) $team->leader_id
            !==
            (int) Auth::id()
        ) {
            $pendingRequestsQuery->where(
                'user_id',
                Auth::id()
            );
        }

        $pendingRequests =
            $pendingRequestsQuery->get();

        return view(
            'teams.show',
            compact(
                'team',
                'pendingRequests'
            )
        );
    }

    public function edit(Team $team): View
    {
        abort_unless(
            (int) $team->leader_id
            ===
            (int) Auth::id(),
            403
        );

        $games = Game::query()
            ->orderBy('name')
            ->get();

        return view(
            'teams.edit',
            compact(
                'team',
                'games'
            )
        );
    }

    public function store(
        StoreTeamRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $data['leader_id'] =
            Auth::id();

        if ($request->hasFile('logo')) {
            $data['logo'] =
                $request
                    ->file('logo')
                    ->store(
                        'team-logos',
                        'public'
                    );
        }

        $team = Team::create($data);

        return redirect()
            ->route(
                'teams.show',
                $team
            )
            ->with(
                'success',
                'Team created successfully.'
            );
    }

    public function update(
        UpdateTeamRequest $request,
        Team $team
    ): RedirectResponse {
        abort_unless(
            (int) $team->leader_id
            ===
            (int) Auth::id(),
            403
        );

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($team->logo) {
                Storage::disk('public')
                    ->delete(
                        $team->logo
                    );
            }

            $data['logo'] =
                $request
                    ->file('logo')
                    ->store(
                        'team-logos',
                        'public'
                    );
        }

        $team->update($data);

        return redirect()
            ->route(
                'teams.show',
                $team
            )
            ->with(
                'success',
                'Team updated successfully.'
            );
    }
}