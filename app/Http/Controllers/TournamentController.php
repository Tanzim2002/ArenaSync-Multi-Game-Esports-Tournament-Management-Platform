<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tournament\StoreTournamentRequest;
use App\Http\Requests\Tournament\UpdateTournamentRequest;
use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('tournaments.index', compact('tournaments'));
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

        return view('tournaments.manage', compact('tournaments'));
    }

    /**
     * Display the tournament creation form.
     */
    public function create(): View
    {
        $games = Game::query()
            ->orderBy('name')
            ->get();

        return view('tournaments.create', compact('games'));
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
            ->route('tournaments.show', $tournament)
            ->with('success', 'Tournament draft created successfully.');
    }

    /**
     * Display a tournament.
     */
    public function show(
        Request $request,
        Tournament $tournament
    ): View {
        $user = $request->user();

        $canViewDraft = $user !== null
            && (
                $tournament->organizer_id === $user->id
                || $user->hasRole(User::ROLE_ADMIN)
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

        return view('tournaments.show', compact('tournament'));
    }

    /**
     * Display the tournament editing form.
     */
    public function edit(
        Request $request,
        Tournament $tournament
    ): View {
        $this->ensureOrganizerOwnsDraft($request, $tournament);

        $games = Game::query()
            ->orderBy('name')
            ->get();

        return view('tournaments.edit', compact(
            'tournament',
            'games'
        ));
    }

    /**
     * Update an organizer's draft tournament.
     */
    public function update(
        UpdateTournamentRequest $request,
        Tournament $tournament
    ): RedirectResponse {
        $tournament->update($request->validated());

        return redirect()
            ->route('tournaments.show', $tournament)
            ->with('success', 'Tournament updated successfully.');
    }

    /**
     * Ensure the authenticated organizer owns the draft tournament.
     */
    private function ensureOrganizerOwnsDraft(
        Request $request,
        Tournament $tournament
    ): void {
        $user = $request->user();

        abort_unless(
            $user !== null
            && $user->hasRole(User::ROLE_ORGANIZER)
            && $tournament->organizer_id === $user->id
            && $tournament->status === Tournament::STATUS_DRAFT,
            403
        );
    }
}