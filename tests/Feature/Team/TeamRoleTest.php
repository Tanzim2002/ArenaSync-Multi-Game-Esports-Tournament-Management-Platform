<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_leader_can_change_member_role(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = Team::create([
            'leader_id' => $leader->id,
            'name' => 'Role Test Team',
            'preferred_game_id' => 1,
            'description' => 'Testing team roles',
        ]);

        $teamMember = TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'role' => TeamMember::ROLE_MEMBER,
            'joined_at' => now(),
        ]);

        $response = $this
            ->actingAs($leader)
            ->patch(
                route('teams.members.role.update', [$team, $teamMember]),
                [
                    'role' => TeamMember::ROLE_CAPTAIN,
                ]
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('team_members', [
            'id' => $teamMember->id,
            'role' => TeamMember::ROLE_CAPTAIN,
        ]);
    }

    public function test_team_leader_can_change_member_to_substitute(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = Team::create([
            'leader_id' => $leader->id,
            'name' => 'Substitute Test Team',
            'preferred_game_id' => 1,
            'description' => 'Testing substitute role',
        ]);

        $teamMember = TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'role' => TeamMember::ROLE_MEMBER,
            'joined_at' => now(),
        ]);

        $response = $this
            ->actingAs($leader)
            ->patch(
                route('teams.members.role.update', [$team, $teamMember]),
                [
                    'role' => TeamMember::ROLE_SUBSTITUTE,
                ]
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('team_members', [
            'id' => $teamMember->id,
            'role' => TeamMember::ROLE_SUBSTITUTE,
        ]);
    }

    public function test_non_leader_cannot_change_member_role(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $otherUser = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = Team::create([
            'leader_id' => $leader->id,
            'name' => 'Permission Test Team',
            'preferred_game_id' => 1,
            'description' => 'Testing role permissions',
        ]);

        $teamMember = TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'role' => TeamMember::ROLE_MEMBER,
            'joined_at' => now(),
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->patch(
                route('teams.members.role.update', [$team, $teamMember]),
                [
                    'role' => TeamMember::ROLE_CAPTAIN,
                ]
            );

        $response->assertStatus(403);

        $this->assertDatabaseHas('team_members', [
            'id' => $teamMember->id,
            'role' => TeamMember::ROLE_MEMBER,
        ]);
    }

    public function test_member_can_leave_team(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = Team::create([
            'leader_id' => $leader->id,
            'name' => 'Leave Test Team',
            'preferred_game_id' => 1,
            'description' => 'Testing leave team',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'role' => TeamMember::ROLE_MEMBER,
            'joined_at' => now(),
        ]);

        $response = $this
            ->actingAs($player)
            ->delete(route('teams.leave', $team));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'user_id' => $player->id,
        ]);
    }

    public function test_team_leader_cannot_leave_team_using_normal_leave_action(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = Team::create([
            'leader_id' => $leader->id,
            'name' => 'Leader Leave Test Team',
            'preferred_game_id' => 1,
            'description' => 'Testing leader leave restriction',
        ]);

        $response = $this
            ->actingAs($leader)
            ->delete(route('teams.leave', $team));

        $response->assertStatus(403);
    }
}