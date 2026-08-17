<?php

namespace App\Http\Controllers;

use App\Http\Requests\Registration\StoreRegistrationRequest;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Display the tournament registration form.
     */
    public function create(
        Request $request,
        Tournament $tournament
    ): View {
        $teams = $request->user()
            ->ledTeams()
            ->orderBy('name')
            ->get();

        $registeredTeamIds = $tournament
            ->registrations()
            ->whereIn(
                'team_id',
                $teams->pluck('id')
            )
            ->pluck('team_id')
            ->all();

        $activeRegistrationCount = $tournament
            ->registrations()
            ->whereNotIn('status', [
                Registration::STATUS_REJECTED,
                Registration::STATUS_CANCELLED,
            ])
            ->count();

        return view(
            'registrations.create',
            compact(
                'tournament',
                'teams',
                'registeredTeamIds',
                'activeRegistrationCount'
            )
        );
    }

    /**
     * Submit a team registration for a tournament.
     */
    public function store(
        StoreRegistrationRequest $request,
        Tournament $tournament
    ): RedirectResponse {
        $team = Team::query()
            ->findOrFail(
                $request->validated('team_id')
            );

        if ($team->leader_id !== $request->user()->id) {
            abort(403);
        }

        if (
            $tournament->status
            !== Tournament::STATUS_REGISTRATION_OPEN
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'registration' =>
                        'Registration is not currently open for this tournament.',
                ]);
        }

        if ($tournament->registration_deadline->isPast()) {
            return back()
                ->withInput()
                ->withErrors([
                    'registration' =>
                        'The registration deadline for this tournament has passed.',
                ]);
        }

        if (
            $team->preferred_game_id !== null
            && $team->preferred_game_id !== $tournament->game_id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'team_id' =>
                        'The selected team does not match this tournament game.',
                ]);
        }

        $alreadyRegistered = $tournament
            ->registrations()
            ->where('team_id', $team->id)
            ->exists();

        if ($alreadyRegistered) {
            return back()
                ->withInput()
                ->withErrors([
                    'team_id' =>
                        'This team has already registered for this tournament.',
                ]);
        }

        $activeRegistrationCount = $tournament
            ->registrations()
            ->whereNotIn('status', [
                Registration::STATUS_REJECTED,
                Registration::STATUS_CANCELLED,
            ])
            ->count();

        if ($activeRegistrationCount >= $tournament->team_limit) {
            return back()
                ->withInput()
                ->withErrors([
                    'registration' =>
                        'This tournament has reached its team registration limit.',
                ]);
        }

        $tournament->registrations()->create([
            'team_id' => $team->id,
            'status' => Registration::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route(
                'tournaments.show',
                $tournament
            )
            ->with(
                'success',
                'Tournament registration submitted successfully. Your registration is pending approval.'
            );
    }
}