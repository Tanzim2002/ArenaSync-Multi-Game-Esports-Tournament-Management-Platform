<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
    /**
     * Team leader changes a member's team role.
     */
    public function updateRole(
        Request $request,
        Team $team,
        TeamMember $teamMember
    ) {
        // Only the team leader can change roles.
        abort_unless($team->leader_id === Auth::id(), 403);

        // Make sure this member actually belongs to this team.
        abort_unless($teamMember->team_id === $team->id, 404);

        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in([
                    TeamMember::ROLE_CAPTAIN,
                    TeamMember::ROLE_MEMBER,
                    TeamMember::ROLE_SUBSTITUTE,
                ]),
            ],
        ]);

        $teamMember->update([
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Team member role updated successfully.');
    }

    /**
     * Logged-in member leaves a team.
     */
    public function leave(Team $team)
    {
        // Team leader cannot leave using the normal leave action.
        abort_if(
            $team->leader_id === Auth::id(),
            403,
            'Team leader cannot leave the team.'
        );

        $teamMember = TeamMember::where('team_id', $team->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $teamMember->delete();

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'You have left the team successfully.');
    }
}