<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchResult;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MatchResultController extends Controller
{
    /**
     * Show the result submission form for a match.
     */
    public function create(Request $request, GameMatch $match): View
    {
        $this->ensureParticipantCanSubmit($request, $match);

        $match->load(['tournament', 'teamOne', 'teamTwo']);

        $result = MatchResult::where('match_id', $match->id)->first();

        abort_if(
            $result !== null && $result->isVerified(),
            403,
            'This match result has already been verified.'
        );

        return view('match-results.create', compact('match', 'result'));
    }

    /**
     * Submit or update a match result.
     */
    public function store(Request $request, GameMatch $match): RedirectResponse
    {
        $this->ensureParticipantCanSubmit($request, $match);

        abort_if(
            $match->status === GameMatch::STATUS_CANCELLED,
            422,
            'A result cannot be submitted for a cancelled match.'
        );

        $validated = $request->validate([
            'team_one_score' => ['required', 'integer', 'min:0'],
            'team_two_score' => ['required', 'integer', 'min:0'],
            'winner_team_id' => [
                'nullable',
                'integer',
                Rule::in(array_filter([
                    $match->team_one_id,
                    $match->team_two_id,
                ])),
            ],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->validateWinner(
            $validated['team_one_score'],
            $validated['team_two_score'],
            $validated['winner_team_id'] ?? null,
            $match
        );

        $existingResult = MatchResult::where('match_id', $match->id)->first();

        abort_if(
            $existingResult !== null && $existingResult->isVerified(),
            403,
            'A verified result cannot be changed.'
        );

        MatchResult::updateOrCreate(
            ['match_id' => $match->id],
            [
                'team_one_score' => $validated['team_one_score'],
                'team_two_score' => $validated['team_two_score'],
                'winner_team_id' => $validated['winner_team_id'] ?? null,
                'status' => MatchResult::STATUS_PENDING,
                'remarks' => $validated['remarks'] ?? null,
                'submitted_by' => $request->user()->id,
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]
        );

        return redirect()
            ->route('matches.show', $match)
            ->with('success', 'Match result submitted for organizer verification.');
    }

    /**
     * Show pending result submissions for one tournament.
     */
    public function index(Request $request, $tournament): View
    {
        $tournamentModel = \App\Models\Tournament::findOrFail($tournament);

        $this->ensureOrganizerOwnsTournament($request, $tournamentModel);

        $results = MatchResult::query()
            ->with([
                'match.teamOne',
                'match.teamTwo',
                'submittedBy',
            ])
            ->whereHas('match', function ($query) use ($tournamentModel) {
                $query->where('tournament_id', $tournamentModel->id);
            })
            ->latest('submitted_at')
            ->get();

        return view('match-results.index', [
            'tournament' => $tournamentModel,
            'results' => $results,
        ]);
    }

    /**
     * Verify a submitted result.
     */
    public function verify(
        Request $request,
        MatchResult $result
    ): RedirectResponse {
        $result->load('match.tournament');

        $this->ensureOrganizerOwnsTournament(
            $request,
            $result->match->tournament
        );

        abort_if(
            ! $result->isPending(),
            422,
            'Only pending results can be verified.'
        );

        DB::transaction(function () use ($request, $result) {
            $result->update([
                'status' => MatchResult::STATUS_VERIFIED,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_notes' => null,
            ]);

            $result->match->update([
                'status' => GameMatch::STATUS_COMPLETED,
            ]);
        });

        return back()->with('success', 'Match result verified successfully.');
    }

    /**
     * Reject a submitted result for correction.
     */
    public function reject(
        Request $request,
        MatchResult $result
    ): RedirectResponse {
        $result->load('match.tournament');

        $this->ensureOrganizerOwnsTournament(
            $request,
            $result->match->tournament
        );

        abort_if(
            ! $result->isPending(),
            422,
            'Only pending results can be rejected.'
        );

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
        ]);

        $result->update([
            'status' => MatchResult::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'],
        ]);

        return back()->with(
            'success',
            'Match result rejected and returned for correction.'
        );
    }

    /**
     * Check whether the current user belongs to either team in the match.
     */
    private function ensureParticipantCanSubmit(
        Request $request,
        GameMatch $match
    ): void {
        $user = $request->user();

        abort_if($user === null, 403);

        $teamIds = array_filter([
            $match->team_one_id,
            $match->team_two_id,
        ]);

        $isLeader = Team::query()
            ->whereIn('id', $teamIds)
            ->where('leader_id', $user->id)
            ->exists();

        $isMember = TeamMember::query()
            ->whereIn('team_id', $teamIds)
            ->where('user_id', $user->id)
            ->exists();

        abort_unless(
            $isLeader || $isMember,
            403,
            'Only participants of this match can submit its result.'
        );
    }

    /**
     * Check that the current user owns the tournament as organizer.
     */
    private function ensureOrganizerOwnsTournament(
        Request $request,
        $tournament
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
     * Ensure winner selection agrees with the submitted scores.
     */
    private function validateWinner(
        int $teamOneScore,
        int $teamTwoScore,
        ?int $winnerTeamId,
        GameMatch $match
    ): void {
        if ($teamOneScore === $teamTwoScore) {
            abort_if(
                $winnerTeamId !== null,
                422,
                'A tied score cannot have a winner.'
            );

            return;
        }

        abort_if(
            $winnerTeamId === null,
            422,
            'Please select the winning team.'
        );

        $expectedWinnerId = $teamOneScore > $teamTwoScore
            ? $match->team_one_id
            : $match->team_two_id;

        abort_if(
            $winnerTeamId !== $expectedWinnerId,
            422,
            'The selected winner does not match the submitted scores.'
        );
    }
}
