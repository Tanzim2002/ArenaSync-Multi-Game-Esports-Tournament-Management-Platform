<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StoreLivestreamRequest;
use App\Http\Requests\Payments\UpdateLivestreamRequest;
use App\Models\Livestream;
use App\Models\Tournament;
use App\Policies\LivestreamPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LivestreamController extends Controller
{
    public function __construct(protected LivestreamPolicy $policy) {}

    // Public: anyone can view active links
    public function index(Tournament $tournament): View
    {
        $livestreams = $tournament->livestreams()->active()->latest()->get();
        return view('payments.livestreams.index', compact('tournament', 'livestreams'));
    }

    public function create(Tournament $tournament): View
    {
        $this->authorizeManage($tournament);
        return view('payments.livestreams.create', compact('tournament'));
    }

    public function store(StoreLivestreamRequest $request, Tournament $tournament): RedirectResponse
    {
        $this->authorizeManage($tournament);

        $tournament->livestreams()->create([
            ...$request->validated(),
            'added_by' => auth()->id(),
        ]);

        return redirect()->route('tournaments.livestreams.index', $tournament)
            ->with('success', 'Livestream link added.');
    }

    public function edit(Tournament $tournament, Livestream $livestream): View
    {
        $this->authorizeManage($tournament);
        return view('payments.livestreams.edit', compact('tournament', 'livestream'));
    }

    public function update(UpdateLivestreamRequest $request, Tournament $tournament, Livestream $livestream): RedirectResponse
    {
        $this->authorizeManage($tournament);
        $livestream->update($request->validated());

        return redirect()->route('tournaments.livestreams.index', $tournament)
            ->with('success', 'Livestream link updated.');
    }

    public function destroy(Tournament $tournament, Livestream $livestream): RedirectResponse
    {
        $this->authorizeManage($tournament);
        $livestream->delete();

        return redirect()->route('tournaments.livestreams.index', $tournament)
            ->with('success', 'Livestream link removed.');
    }

    protected function authorizeManage(Tournament $tournament): void
    {
        if (! $this->policy->manage(auth()->user(), $tournament)) {
            abort(403, 'You are not authorized to manage livestreams for this tournament.');
        }
    }
}