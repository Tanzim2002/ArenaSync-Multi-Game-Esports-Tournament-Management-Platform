<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameMatch\StoreGameMatchRequest;
use App\Models\GameMatch;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MatchController extends Controller
{
    /**
     * Display the public match timeline for a tournament.
     */
    public function index(Tournament $tournament): View
    {
        $matches = $tournament
            ->matches()
            ->with(['teamOne', 'teamTwo'])
            ->orderBy('scheduled_at')
            ->get();

        return view('matches.index', compact('tournament', 'matches'));
    }

    /**
     * Display the match scheduling form.
     */
    public function create(Request $request, Tournament $tournament): View
    {
        $this->ensureOrganizerOwnsTournament($request, $tournament);

        $teams = $this->eligibleTeams($tournament);

        return view('matches.create', compact('tournament', 'teams'));
    }

    /**
     * Schedule a match between confirmed/eligible participants.
     */
    public function store(StoreGameMatchRequest $request, Tournament $tournament): RedirectResponse
    {
        $this->ensureOrganizerOwnsTournament($request, $tournament);

        $data = $request->validated();
        $eligibleTeamIds = $this->eligibleTeamIds($tournament);

        if (! in_array((int) $data['team_one_id'], $eligibleTeamIds, true)) {
            return back()->withInput()->withErrors([
                'team_one_id' => 'Team one is not a confirmed participant of this tournament.',
            ]);
        }

        if ($data['team_two_id'] !== null && ! in_array((int) $data['team_two_id'], $eligibleTeamIds, true)) {
            return back()->withInput()->withErrors([
                'team_two_id' => 'Team two is not a confirmed participant of this tournament.',
            ]);
        }

        $scheduledAt = Carbon::parse($data['scheduled_at']);

        if ($scheduledAt->lt($tournament->start_at) || $scheduledAt->gt($tournament->end_at)) {
            return back()->withInput()->withErrors([
                'scheduled_at' => 'The match must be scheduled within the tournament start and end dates.',
            ]);
        }

        $overlap = GameMatch::query()
            ->where('tournament_id', $tournament->id)
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', GameMatch::STATUS_CANCELLED)
            ->where(function ($query) use ($data) {
                $query->where('team_one_id', $data['team_one_id'])->orWhere('team_two_id', $data['team_one_id']);

                if ($data['team_two_id'] !== null) {
                    $query->orWhere('team_one_id', $data['team_two_id'])->orWhere('team_two_id', $data['team_two_id']);
                }
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'scheduled_at' => 'One of the selected teams already has a match scheduled at this time.',
            ]);
        }

        if ($data['team_two_id'] !== null) {
            $duplicate = GameMatch::query()
                ->where('tournament_id', $tournament->id)
                ->where('round', $data['round'])
                ->where('status', '!=', GameMatch::STATUS_CANCELLED)
                ->where(function ($query) use ($data) {
                    $query->where(function ($q) use ($data) {
                        $q->where('team_one_id', $data['team_one_id'])->where('team_two_id', $data['team_two_id']);
                    })->orWhere(function ($q) use ($data) {
                        $q->where('team_one_id', $data['team_two_id'])->where('team_two_id', $data['team_one_id']);
                    });
                })
                ->exists();

            if ($duplicate) {
                return back()->withInput()->withErrors([
                    'team_two_id' => 'These two teams are already scheduled to play in this round.',
                ]);
            }
        }

        $tournament->matches()->create([
            'round' => $data['round'],
            'team_one_id' => $data['team_one_id'],
            'team_two_id' => $data['team_two_id'],
            'scheduled_at' => $scheduledAt,
            'status' => GameMatch::STATUS_SCHEDULED,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('tournaments.matches.index', $tournament)->with('success', 'Match scheduled successfully.');
    }

    /**
     * Display a single match.
     */
    public function show(GameMatch $match): View
    {
        $match->load(['tournament', 'teamOne', 'teamTwo']);

        return view('matches.show', compact('match'));
    }

    /**
     * Move a match to a valid next lifecycle status.
     */
    public function changeStatus(Request $request, GameMatch $match): RedirectResponse
    {
        $this->ensureOrganizerOwnsTournament($request, $match->tournament);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(GameMatch::statuses())],
        ]);

        if (! $match->canTransitionTo($validated['status'])) {
            return back()->withErrors([
                'status' => 'Invalid match status transition from '.$match->status.' to '.$validated['status'].'.',
            ]);
        }

        $match->transitionTo($validated['status']);

        return back()->with('success', 'Match status updated to '.$validated['status'].'.');
    }

    /**
     * Contract: GET /api/matches/{match} — consumed by Member 04 (F14 Result Submission).
     */
    public function apiShow(GameMatch $match): JsonResponse
    {
        $match->load(['teamOne', 'teamTwo']);

        return response()->json([
            'id' => $match->id,
            'tournament_id' => $match->tournament_id,
            'round' => $match->round,
            'team_one_id' => $match->team_one_id,
            'team_two_id' => $match->team_two_id,
            'scheduled_at' => $match->scheduled_at,
            'status' => $match->status,
        ]);
    }

    /**
     * Contract: GET /api/matches/upcoming — consumed by Member 02 (F20 Dashboard).
     */
    public function apiUpcoming(Request $request): JsonResponse
    {
        $matches = GameMatch::query()
            ->where('status', GameMatch::STATUS_SCHEDULED)
            ->where('scheduled_at', '>=', now())
            ->with(['teamOne', 'teamTwo'])
            ->orderBy('scheduled_at')
            ->limit(20)
            ->get();

        return response()->json($matches->map(fn ($match) => [
            'id' => $match->id,
            'tournament_id' => $match->tournament_id,
            'round' => $match->round,
            'team_one' => $match->teamOne?->name,
            'team_two' => $match->teamTwo?->name,
            'scheduled_at' => $match->scheduled_at,
            'status' => $match->status,
        ]));
    }

    /**
     * Team IDs eligible to be scheduled: approved registration, and — for
     * paid tournaments — a verified payment (Sprint 04 "confirmed/eligible
     * participant" contract).
     *
     * @return list<int>
     */
    private function eligibleTeamIds(Tournament $tournament): array
    {
        return Registration::query()
            ->where('tournament_id', $tournament->id)
            ->where('status', Registration::STATUS_APPROVED)
            ->when(
                $tournament->is_paid,
                fn ($query) => $query->whereHas('payment', fn ($q) => $q->where('status', Payment::STATUS_VERIFIED))
            )
            ->pluck('team_id')
            ->all();
    }

    private function eligibleTeams(Tournament $tournament): \Illuminate\Database\Eloquent\Collection
    {
        return Team::query()
            ->whereIn('id', $this->eligibleTeamIds($tournament))
            ->orderBy('name')
            ->get();
    }

    /**
     * Ensure the authenticated user is the organizer who owns this tournament.
     */
    private function ensureOrganizerOwnsTournament(Request $request, Tournament $tournament): void
    {
        $user = $request->user();

        abort_unless(
            $user !== null && $user->hasRole(User::ROLE_ORGANIZER) && $tournament->organizer_id === $user->id,
            403
        );
    }
}