<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamMembershipRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMembershipRequestTest extends TestCase
{
    use RefreshDatabase;

    private function createTeam(User $leader): Team
    {
        return Team::create([
            'leader_id' => $leader->id,
            'name' => 'Test Team',
            'description' => 'Feature 5 test team',
        ]);
    }

    public function test_team_leader_can_invite_a_player(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = $this->createTeam($leader);

        $response = $this
            ->actingAs($leader)
            ->post(
                route('teams.invitations.store', $team),
                [
                    'email' => $player->email,
                ]
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('team_membership_requests', [
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $leader->id,
            'request_type' => TeamMembershipRequest::TYPE_INVITATION,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);
    }

    public function test_duplicate_invitation_is_prevented(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = $this->createTeam($leader);

        TeamMembershipRequest::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $leader->id,
            'request_type' => TeamMembershipRequest::TYPE_INVITATION,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);

        $this
            ->actingAs($leader)
            ->post(
                route('teams.invitations.store', $team),
                [
                    'email' => $player->email,
                ]
            );

        $this->assertDatabaseCount(
            'team_membership_requests',
            1
        );
    }

    public function test_invited_player_can_accept_invitation(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = $this->createTeam($leader);

        $membershipRequest = TeamMembershipRequest::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $leader->id,
            'request_type' => TeamMembershipRequest::TYPE_INVITATION,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($player)
            ->patch(
                route(
                    'team-membership-requests.accept',
                    $membershipRequest
                )
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('team_members', [
            'team_id' => $team->id,
            'user_id' => $player->id,
        ]);

        $this->assertDatabaseHas('team_membership_requests', [
            'id' => $membershipRequest->id,
            'status' => TeamMembershipRequest::STATUS_ACCEPTED,
            'responded_by_id' => $player->id,
        ]);
    }

    public function test_player_can_request_to_join_team(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = $this->createTeam($leader);

        $response = $this
            ->actingAs($player)
            ->post(
                route('teams.join-requests.store', $team)
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('team_membership_requests', [
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $player->id,
            'request_type' => TeamMembershipRequest::TYPE_JOIN_REQUEST,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);
    }

    public function test_team_leader_can_accept_join_request(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = $this->createTeam($leader);

        $membershipRequest = TeamMembershipRequest::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $player->id,
            'request_type' => TeamMembershipRequest::TYPE_JOIN_REQUEST,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($leader)
            ->patch(
                route(
                    'team-membership-requests.accept',
                    $membershipRequest
                )
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('team_members', [
            'team_id' => $team->id,
            'user_id' => $player->id,
        ]);

        $this->assertDatabaseHas('team_membership_requests', [
            'id' => $membershipRequest->id,
            'status' => TeamMembershipRequest::STATUS_ACCEPTED,
            'responded_by_id' => $leader->id,
        ]);
    }

    public function test_team_leader_can_reject_join_request(): void
    {
        $leader = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $player = User::factory()->create([
            'role' => 'PLAYER',
        ]);

        $team = $this->createTeam($leader);

        $membershipRequest = TeamMembershipRequest::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'requested_by_id' => $player->id,
            'request_type' => TeamMembershipRequest::TYPE_JOIN_REQUEST,
            'status' => TeamMembershipRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($leader)
            ->patch(
                route(
                    'team-membership-requests.reject',
                    $membershipRequest
                )
            );

        $response->assertStatus(302);

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'user_id' => $player->id,
        ]);

        $this->assertDatabaseHas('team_membership_requests', [
            'id' => $membershipRequest->id,
            'status' => TeamMembershipRequest::STATUS_REJECTED,
            'responded_by_id' => $leader->id,
        ]);
    }
}