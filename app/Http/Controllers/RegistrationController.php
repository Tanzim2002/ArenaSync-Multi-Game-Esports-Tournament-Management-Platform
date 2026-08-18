<?php

namespace App\Http\Controllers;

use App\Http\Requests\Registration\StoreRegistrationRequest;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
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

    /**
     * Display registrations submitted to an organizer-owned tournament.
     */
    public function index(
        Request $request,
        Tournament $tournament
    ): View {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $tournament->load([
            'game',
            'organizer',
        ]);

        $registrations = $tournament
            ->registrations()
            ->with([
                'team.leader',
            ])
            ->orderByRaw(
                'CASE WHEN status = ? THEN 0 ELSE 1 END',
                [
                    Registration::STATUS_PENDING,
                ]
            )
            ->orderByDesc('submitted_at')
            ->paginate(15);

        $pendingCount = $tournament
            ->registrations()
            ->where(
                'status',
                Registration::STATUS_PENDING
            )
            ->count();

        $approvedCount = $tournament
            ->registrations()
            ->where(
                'status',
                Registration::STATUS_APPROVED
            )
            ->count();

        $rejectedCount = $tournament
            ->registrations()
            ->where(
                'status',
                Registration::STATUS_REJECTED
            )
            ->count();

        return view(
            'registrations.index',
            compact(
                'tournament',
                'registrations',
                'pendingCount',
                'approvedCount',
                'rejectedCount'
            )
        );
    }

    /**
     * Approve a pending tournament registration.
     */
    public function approve(
        Request $request,
        Tournament $tournament,
        Registration $registration
    ): RedirectResponse {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $this->ensureRegistrationBelongsToTournament(
            $registration,
            $tournament
        );

        if (! $registration->approve()) {
            return back()
                ->withErrors([
                    'registration' =>
                        'Only pending registrations can be approved.',
                ]);
        }

        return back()
            ->with(
                'success',
                'Team registration approved successfully.'
            );
    }

    /**
     * Reject a pending tournament registration.
     */
    public function reject(
        Request $request,
        Tournament $tournament,
        Registration $registration
    ): RedirectResponse {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $this->ensureRegistrationBelongsToTournament(
            $registration,
            $tournament
        );

        if (! $registration->reject()) {
            return back()
                ->withErrors([
                    'registration' =>
                        'Only pending registrations can be rejected.',
                ]);
        }

        return back()
            ->with(
                'success',
                'Team registration rejected successfully.'
            );
    }

    /**
     * Ensure the authenticated user owns the tournament as organizer.
     */
    private function ensureOrganizerOwnsTournament(
        Request $request,
        Tournament $tournament
    ): void {
        $user = $request->user();

        abort_unless(
            $user !== null
            && $user->hasRole(User::ROLE_ORGANIZER)
            && $tournament->organizer_id === $user->id,
            403
        );
    }

    /**
     * Ensure the registration belongs to the supplied tournament.
     */
    private function ensureRegistrationBelongsToTournament(
        Registration $registration,
        Tournament $tournament
    ): void {
        abort_unless(
            $registration->tournament_id === $tournament->id,
            404
        );
    }
}