<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tournament\StoreTournamentRequest;
use App\Http\Requests\Tournament\UpdateTournamentRequest;
use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TournamentController extends Controller
{
    /**
     * Display tournaments available to the public.
     */
    public function index(): View
    {
        $tournaments = Tournament::query()
            ->with([
                'game',
                'organizer',
            ])
            ->whereIn('status', [
                Tournament::STATUS_REGISTRATION_OPEN,
                Tournament::STATUS_REGISTRATION_CLOSED,
                Tournament::STATUS_ONGOING,
                Tournament::STATUS_COMPLETED,
                Tournament::STATUS_CANCELLED,
            ])
            ->orderBy('start_at')
            ->paginate(12);

        return view(
            'tournaments.index',
            compact('tournaments')
        );
    }

    /**
     * Display tournaments owned by the authenticated organizer.
     */
    public function manage(Request $request): View
    {
        $tournaments = $request->user()
            ->organizedTournaments()
            ->with('game')
            ->latest()
            ->paginate(10);

        return view(
            'tournaments.manage',
            compact('tournaments')
        );
    }

    /**
     * Display the tournament creation form.
     */
    public function create(): View
    {
        $games = Game::query()
            ->orderBy('name')
            ->get();

        return view(
            'tournaments.create',
            compact('games')
        );
    }

    /**
     * Store a newly created tournament as a draft.
     */
    public function store(
        StoreTournamentRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $data['status'] = Tournament::STATUS_DRAFT;

        $tournament = $request->user()
            ->organizedTournaments()
            ->create($data);

        return redirect()
            ->route(
                'tournaments.show',
                $tournament
            )
            ->with(
                'success',
                'Tournament draft created successfully.'
            );
    }

    /**
     * Display a tournament.
     */
    public function show(
        Request $request,
        Tournament $tournament
    ): View {
        $user = $request->user();

        $canViewDraft = (
            $user !== null
            && (
                $tournament->organizer_id === $user->id
                || $user->hasRole(User::ROLE_ADMIN)
            )
        );

        if (
            $tournament->status === Tournament::STATUS_DRAFT
            && ! $canViewDraft
        ) {
            abort(404);
        }

        $tournament->load([
            'game',
            'organizer',
        ]);

        return view(
            'tournaments.show',
            compact('tournament')
        );
    }

    /**
     * Display the edit form for an owned draft tournament.
     */
    public function edit(
        Request $request,
        Tournament $tournament
    ): View {
        $this->ensureOrganizerOwnsDraft(
            $request,
            $tournament
        );

        $games = Game::query()
            ->orderBy('name')
            ->get();

        return view(
            'tournaments.edit',
            compact(
                'tournament',
                'games'
            )
        );
    }

    /**
     * Update an organizer-owned draft tournament.
     */
    public function update(
        UpdateTournamentRequest $request,
        Tournament $tournament
    ): RedirectResponse {
        $tournament->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'tournaments.show',
                $tournament
            )
            ->with(
                'success',
                'Tournament updated successfully.'
            );
    }

    /**
     * Publish a draft tournament and open registration.
     */
    public function publish(
        Request $request,
        Tournament $tournament
    ): RedirectResponse {
        $this->ensureOrganizerOwnsDraft(
            $request,
            $tournament
        );

        if (
            $tournament
                ->registration_deadline
                ->isPast()
        ) {
            return back()
                ->withErrors([
                    'publish' =>
                        'The registration deadline must be in the future before publishing.',
                ]);
        }

        if (
            ! $tournament->transitionTo(
                Tournament::STATUS_REGISTRATION_OPEN
            )
        ) {
            return back()
                ->withErrors([
                    'status' =>
                        'This tournament cannot be published from its current status.',
                ]);
        }

        return redirect()
            ->route(
                'tournaments.show',
                $tournament
            )
            ->with(
                'success',
                'Tournament published successfully. Registration is now open.'
            );
    }

    /**
     * Move an organizer-owned tournament to a valid next lifecycle status.
     */
    public function changeStatus(
        Request $request,
        Tournament $tournament
    ): RedirectResponse {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(
                    Tournament::statuses()
                ),
            ],
        ]);

        $nextStatus = $validated['status'];

        if (
            $nextStatus === Tournament::STATUS_REGISTRATION_OPEN
        ) {
            return back()
                ->withErrors([
                    'status' =>
                        'Use the Publish action to open tournament registration.',
                ]);
        }

        if (
            $nextStatus === Tournament::STATUS_CANCELLED
        ) {
            return back()
                ->withErrors([
                    'status' =>
                        'Use the Cancel action to cancel a tournament.',
                ]);
        }

        if (
            ! $tournament->canTransitionTo(
                $nextStatus
            )
        ) {
            return back()
                ->withErrors([
                    'status' =>
                        'Invalid tournament status transition from '
                        . str_replace('_', ' ', $tournament->status)
                        . ' to '
                        . str_replace('_', ' ', $nextStatus)
                        . '.',
                ]);
        }

        $tournament->transitionTo(
            $nextStatus
        );

        return redirect()
            ->route(
                'tournaments.show',
                $tournament
            )
            ->with(
                'success',
                'Tournament status updated to '
                . str_replace('_', ' ', $nextStatus)
                . '.'
            );
    }

    /**
     * Cancel an organizer-owned tournament.
     */
    public function cancel(
        Request $request,
        Tournament $tournament
    ): RedirectResponse {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        if (
            ! $tournament->canTransitionTo(
                Tournament::STATUS_CANCELLED
            )
        ) {
            return back()
                ->withErrors([
                    'cancel' =>
                        'A completed or already cancelled tournament cannot be cancelled.',
                ]);
        }

        $tournament->transitionTo(
            Tournament::STATUS_CANCELLED
        );

        return redirect()
            ->route(
                'tournaments.show',
                $tournament
            )
            ->with(
                'success',
                'Tournament cancelled successfully.'
            );
    }

    /**
     * Ensure the authenticated user is the organizer
     * who owns this tournament.
     */
    private function ensureOrganizerOwnsTournament(
        Request $request,
        Tournament $tournament
    ): void {
        $user = $request->user();

        abort_unless(
            $user !== null
            && $user->hasRole(
                User::ROLE_ORGANIZER
            )
            && $tournament->organizer_id === $user->id,
            403
        );
    }

    /**
     * Ensure the organizer owns a draft tournament.
     */
    private function ensureOrganizerOwnsDraft(
        Request $request,
        Tournament $tournament
    ): void {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        abort_unless(
            $tournament->status
                === Tournament::STATUS_DRAFT,
            403
        );
    }
}