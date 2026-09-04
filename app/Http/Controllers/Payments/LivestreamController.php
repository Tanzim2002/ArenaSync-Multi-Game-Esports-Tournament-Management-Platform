<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StoreLivestreamRequest;
use App\Http\Requests\Payments\UpdateLivestreamRequest;
use App\Models\Livestream;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LivestreamController extends Controller
{
    public function index(Tournament $tournament): View
    {
        $canManage = $this->canManage($tournament);

        $livestreams = $tournament
            ->livestreams()
            ->when(
                ! $canManage,
                fn ($query) =>
                    $query->where(
                        'is_active',
                        true
                    )
            )
            ->latest()
            ->get();

        return view(
            'payments.livestreams.index',
            compact(
                'tournament',
                'livestreams',
                'canManage'
            )
        );
    }

    public function create(Tournament $tournament): View
    {
        $this->authorizeManage($tournament);

        return view(
            'payments.livestreams.create',
            compact('tournament')
        );
    }

    public function store(
        StoreLivestreamRequest $request,
        Tournament $tournament
    ): RedirectResponse {
        $this->authorizeManage($tournament);

        $data = $request->validated();

        $tournament->livestreams()->create([
            'platform' => $data['platform'],
            'url' => $data['url'],
            'label' => $data['label'] ?? null,
            'is_active' => true,
            'added_by' => auth()->id(),
        ]);

        return redirect()
            ->route(
                'tournaments.livestreams.index',
                $tournament
            )
            ->with(
                'success',
                'Livestream link added.'
            );
    }

    public function edit(
        Tournament $tournament,
        Livestream $livestream
    ): View {
        $this->authorizeManage($tournament);
        $this->ensureBelongsToTournament(
            $livestream,
            $tournament
        );

        return view(
            'payments.livestreams.edit',
            compact(
                'tournament',
                'livestream'
            )
        );
    }

    public function update(
        UpdateLivestreamRequest $request,
        Tournament $tournament,
        Livestream $livestream
    ): RedirectResponse {
        $this->authorizeManage($tournament);
        $this->ensureBelongsToTournament(
            $livestream,
            $tournament
        );

        $livestream->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'tournaments.livestreams.index',
                $tournament
            )
            ->with(
                'success',
                'Livestream updated.'
            );
    }

    public function destroy(
        Tournament $tournament,
        Livestream $livestream
    ): RedirectResponse {
        $this->authorizeManage($tournament);
        $this->ensureBelongsToTournament(
            $livestream,
            $tournament
        );

        $livestream->delete();

        return redirect()
            ->route(
                'tournaments.livestreams.index',
                $tournament
            )
            ->with(
                'success',
                'Livestream removed.'
            );
    }

    private function canManage(
        Tournament $tournament
    ): bool {
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        return (int) $user->id
            ===
            (int) $tournament->organizer_id;
    }

    private function authorizeManage(
        Tournament $tournament
    ): void {
        abort_unless(
            $this->canManage($tournament),
            403
        );
    }

    private function ensureBelongsToTournament(
        Livestream $livestream,
        Tournament $tournament
    ): void {
        abort_unless(
            (int) $livestream->tournament_id
                ===
            (int) $tournament->id,
            404
        );
    }
}