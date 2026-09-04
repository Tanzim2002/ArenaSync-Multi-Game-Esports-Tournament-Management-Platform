<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(): View
    {
        $sponsors = Sponsor::query()
            ->with('tournament')
            ->latest()
            ->paginate(10);

        return view(
            'sponsors.index',
            compact('sponsors')
        );
    }

    public function create(Request $request): View
    {
        $this->ensureManager($request);

        $tournaments = $this
            ->manageableTournaments($request);

        return view(
            'sponsors.create',
            compact('tournaments')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $this->ensureManager($request);

        $validated = $this
            ->validateSponsor($request);

        $tournament = Tournament::findOrFail(
            $validated['tournament_id']
        );

        $this->ensureCanManageTournament(
            $request,
            $tournament
        );

        Sponsor::create($validated);

        return redirect()
            ->route('sponsors.index')
            ->with(
                'success',
                'Sponsor created successfully.'
            );
    }

    public function show(Sponsor $sponsor): View
    {
        $sponsor->load('tournament');

        return view(
            'sponsors.show',
            compact('sponsor')
        );
    }

    public function edit(
        Request $request,
        Sponsor $sponsor
    ): View {
        $this->ensureManager($request);

        $sponsor->load('tournament');

        $this->ensureCanManageTournament(
            $request,
            $sponsor->tournament
        );

        $tournaments = $this
            ->manageableTournaments($request);

        return view(
            'sponsors.edit',
            compact(
                'sponsor',
                'tournaments'
            )
        );
    }

    public function update(
        Request $request,
        Sponsor $sponsor
    ): RedirectResponse {
        $this->ensureManager($request);

        $sponsor->load('tournament');

        $this->ensureCanManageTournament(
            $request,
            $sponsor->tournament
        );

        $validated = $this
            ->validateSponsor($request);

        $targetTournament =
            Tournament::findOrFail(
                $validated['tournament_id']
            );

        $this->ensureCanManageTournament(
            $request,
            $targetTournament
        );

        $sponsor->update($validated);

        return redirect()
            ->route('sponsors.index')
            ->with(
                'success',
                'Sponsor updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        Sponsor $sponsor
    ): RedirectResponse {
        $this->ensureManager($request);

        $sponsor->load('tournament');

        $this->ensureCanManageTournament(
            $request,
            $sponsor->tournament
        );

        $sponsor->delete();

        return redirect()
            ->route('sponsors.index')
            ->with(
                'success',
                'Sponsor deleted successfully.'
            );
    }

    private function validateSponsor(
        Request $request
    ): array {
        return $request->validate([
            'tournament_id' => [
                'required',
                'exists:tournaments,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'logo' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'website' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'sponsorship_type' => [
                'nullable',
                'string',
                'max:100',
            ],
            'amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);
    }

    private function ensureManager(
        Request $request
    ): void {
        $user = $request->user();

        abort_unless(
            $user !== null
            &&
            $user->hasRole(
                User::ROLE_ORGANIZER,
                User::ROLE_ADMIN
            ),
            403
        );
    }

    private function ensureCanManageTournament(
        Request $request,
        Tournament $tournament
    ): void {
        $user = $request->user();

        abort_unless(
            $user !== null
            &&
            (
                $user->hasRole(
                    User::ROLE_ADMIN
                )
                ||
                (
                    $user->hasRole(
                        User::ROLE_ORGANIZER
                    )
                    &&
                    (int) $tournament->organizer_id
                        ===
                    (int) $user->id
                )
            ),
            403
        );
    }

    private function manageableTournaments(
        Request $request
    ) {
        $user = $request->user();

        return Tournament::query()
            ->when(
                ! $user->hasRole(
                    User::ROLE_ADMIN
                ),
                fn ($query) =>
                    $query->where(
                        'organizer_id',
                        $user->id
                    )
            )
            ->orderBy('title')
            ->get();
    }
}