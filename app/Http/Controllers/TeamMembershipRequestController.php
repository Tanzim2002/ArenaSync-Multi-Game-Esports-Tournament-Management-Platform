<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamMembershipRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamMembershipRequestController extends Controller
{
    /**
     * Team leader sends an invitation to a player.
     */
    public function invite(Request $request, Team $team): RedirectResponse
    {
        // Only the team leader can invite players.
        abort_unless($team->leader_id === Auth::id(), 403);

        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
        ]);

        $player = User::where('email', $data['email'])->firstOrFail();

        // Leader cannot invite themselves.
        if ($player->id === $team->leader_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'The team leader cannot invite themselves.',
                ]);
        }

        // Player cannot be added twice to the same team.
        if ($this->isAlreadyMember($team, $player)) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'This player is already a member of the team.',
                ]);
        }

        // Prevent duplicate/conflicting pending requests.
        if ($this->pendingRequestExists($team, $player)) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'A pending request already exists for this player.',
                ]);
        }

        TeamMembershipRequest::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => Auth::id(),
            'request_type' => TeamMembershipRequest::TYPE_INVITATION,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);

        return back()->with(
            'success',
            'Player invitation sent successfully.'
        );
    }

    /**
     * Player requests to join a team.
     */
    public function requestToJoin(Team $team): RedirectResponse
    {
        $player = Auth::user();

        // Team leader does not need to join their own team.
        if ($player->id === $team->leader_id) {
            return back()->withErrors([
                'team' => 'You are already the leader of this team.',
            ]);
        }

        if ($this->isAlreadyMember($team, $player)) {
            return back()->withErrors([
                'team' => 'You are already a member of this team.',
            ]);
        }

        if ($this->pendingRequestExists($team, $player)) {
            return back()->withErrors([
                'team' => 'A pending request already exists for this team.',
            ]);
        }

        TeamMembershipRequest::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $player->id,
            'request_type' => TeamMembershipRequest::TYPE_JOIN_REQUEST,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);

        return back()->with(
            'success',
            'Join request sent successfully.'
        );
    }

    /**
     * Accept an invitation or join request.
     */
    public function accept(
        TeamMembershipRequest $membershipRequest
    ): RedirectResponse {
        $this->ensurePending($membershipRequest);
        $this->ensureResponsePermission($membershipRequest);

        if (
            TeamMember::where('team_id', $membershipRequest->team_id)
                ->where('user_id', $membershipRequest->user_id)
                ->exists()
        ) {
            return back()->withErrors([
                'request' => 'This player is already a member of the team.',
            ]);
        }

        DB::transaction(function () use ($membershipRequest) {
            TeamMember::create([
                'team_id' => $membershipRequest->team_id,
                'user_id' => $membershipRequest->user_id,
                'joined_at' => now(),
            ]);

            $membershipRequest->update([
                'status' => TeamMembershipRequest::STATUS_ACCEPTED,
                'responded_by_id' => Auth::id(),
                'responded_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            'Membership request accepted successfully.'
        );
    }

    /**
     * Reject an invitation or join request.
     */
    public function reject(
        TeamMembershipRequest $membershipRequest
    ): RedirectResponse {
        $this->ensurePending($membershipRequest);
        $this->ensureResponsePermission($membershipRequest);

        $membershipRequest->update([
            'status' => TeamMembershipRequest::STATUS_REJECTED,
            'responded_by_id' => Auth::id(),
            'responded_at' => now(),
        ]);

        return back()->with(
            'success',
            'Membership request rejected.'
        );
    }

    /**
     * Cancel a request started by the current user.
     */
    public function cancel(
        TeamMembershipRequest $membershipRequest
    ): RedirectResponse {
        $this->ensurePending($membershipRequest);

        abort_unless(
            $membershipRequest->requested_by_id === Auth::id(),
            403
        );

        $membershipRequest->update([
            'status' => TeamMembershipRequest::STATUS_CANCELLED,
            'responded_by_id' => Auth::id(),
            'responded_at' => now(),
        ]);

        return back()->with(
            'success',
            'Membership request cancelled.'
        );
    }

    /**
     * Check if player is already a member.
     */
    private function isAlreadyMember(Team $team, User $player): bool
    {
        return TeamMember::where('team_id', $team->id)
            ->where('user_id', $player->id)
            ->exists();
    }

    /**
     * Check for an existing pending invitation/join request.
     */
    private function pendingRequestExists(
        Team $team,
        User $player
    ): bool {
        return TeamMembershipRequest::where('team_id', $team->id)
            ->where('user_id', $player->id)
            ->where(
                'status',
                TeamMembershipRequest::STATUS_PENDING
            )
            ->exists();
    }

    /**
     * Request must still be pending.
     */
    private function ensurePending(
        TeamMembershipRequest $membershipRequest
    ): void {
        abort_unless(
            $membershipRequest->status
                === TeamMembershipRequest::STATUS_PENDING,
            422
        );
    }

    /**
     * Decide who is allowed to accept/reject.
     */
    private function ensureResponsePermission(
        TeamMembershipRequest $membershipRequest
    ): void {
        if (
            $membershipRequest->request_type
                === TeamMembershipRequest::TYPE_JOIN_REQUEST
        ) {
            // Join request -> team leader responds.
            abort_unless(
                $membershipRequest->team->leader_id === Auth::id(),
                403
            );

            return;
        }

        // Invitation -> invited player responds.
        abort_unless(
            $membershipRequest->user_id === Auth::id(),
            403
        );
    }
}
