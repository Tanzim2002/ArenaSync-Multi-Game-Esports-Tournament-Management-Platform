<?php

namespace Tests\Feature\Registration;

use App\Models\Game;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantApprovalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a team using the same pattern as the F7 registration tests.
     */
    private function createTeam(
        User $leader,
        Game $game,
        string $name = 'Arena Approval Team'
    ): Team {
        return Team::create([
            'leader_id' => $leader->id,
            'name' => $name,
            'preferred_game_id' => $game->id,
            'description' =>
                'Team created for participant approval testing.',
        ]);
    }

    /**
     * Create an organizer-owned tournament.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function createTournament(
        User $organizer,
        Game $game,
        array $overrides = []
    ): Tournament {
        return Tournament::factory()->create(
            array_merge([
                'organizer_id' => $organizer->id,
                'game_id' => $game->id,
                'status' => Tournament::STATUS_REGISTRATION_OPEN,
                'registration_deadline' => now()->addDays(3),
                'start_at' => now()->addDays(5),
                'end_at' => now()->addDays(6),
            ], $overrides)
        );
    }

    /**
     * Create a tournament registration.
     */
    private function createRegistration(
        Tournament $tournament,
        Team $team,
        string $status = Registration::STATUS_PENDING
    ): Registration {
        return Registration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => $status,
            'submitted_at' => now(),
        ]);
    }

    public function test_guest_is_redirected_from_participant_approval_page(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $this->get(
            route(
                'tournaments.registrations.index',
                $tournament
            )
        )->assertRedirect(route('login'));
    }

    public function test_owner_organizer_can_view_tournament_registrations(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game,
            'Approval List Team'
        );

        $this->createRegistration(
            $tournament,
            $team
        );

        $response = $this
            ->actingAs($organizer)
            ->get(
                route(
                    'tournaments.registrations.index',
                    $tournament
                )
            );

        $response
            ->assertOk()
            ->assertSee('Participant Approvals')
            ->assertSee('Approval List Team')
            ->assertSee('PENDING')
            ->assertSee('Approve')
            ->assertSee('Reject');
    }

    public function test_other_organizer_cannot_view_tournament_registrations(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $otherOrganizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $owner,
            $game
        );

        $this
            ->actingAs($otherOrganizer)
            ->get(
                route(
                    'tournaments.registrations.index',
                    $tournament
                )
            )
            ->assertForbidden();
    }

    public function test_owner_organizer_can_approve_pending_registration(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game
        );

        $registration = $this->createRegistration(
            $tournament,
            $team
        );

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.registrations.approve',
                    [
                        $tournament,
                        $registration,
                    ]
                )
            );

        $response
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_APPROVED,
            ]
        );
    }

    public function test_owner_organizer_can_reject_pending_registration(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game
        );

        $registration = $this->createRegistration(
            $tournament,
            $team
        );

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.registrations.reject',
                    [
                        $tournament,
                        $registration,
                    ]
                )
            );

        $response
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_REJECTED,
            ]
        );
    }

    public function test_approved_registration_cannot_be_rejected_again(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game
        );

        $registration = $this->createRegistration(
            $tournament,
            $team,
            Registration::STATUS_APPROVED
        );

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.registrations.reject',
                    [
                        $tournament,
                        $registration,
                    ]
                )
            );

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('registration');

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_APPROVED,
            ]
        );
    }

    public function test_rejected_registration_cannot_be_approved_again(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game
        );

        $registration = $this->createRegistration(
            $tournament,
            $team,
            Registration::STATUS_REJECTED
        );

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.registrations.approve',
                    [
                        $tournament,
                        $registration,
                    ]
                )
            );

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('registration');

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_REJECTED,
            ]
        );
    }

    public function test_other_organizer_cannot_approve_pending_registration(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $otherOrganizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $owner,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game
        );

        $registration = $this->createRegistration(
            $tournament,
            $team
        );

        $this
            ->actingAs($otherOrganizer)
            ->patch(
                route(
                    'tournaments.registrations.approve',
                    [
                        $tournament,
                        $registration,
                    ]
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_PENDING,
            ]
        );
    }

    public function test_registration_from_another_tournament_cannot_be_reviewed(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $firstTournament = $this->createTournament(
            $organizer,
            $game
        );

        $secondTournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $player,
            $game
        );

        $registration = $this->createRegistration(
            $secondTournament,
            $team
        );

        $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.registrations.approve',
                    [
                        $firstTournament,
                        $registration,
                    ]
                )
            )
            ->assertNotFound();

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_PENDING,
            ]
        );
    }

    public function test_player_and_admin_cannot_review_participant_registrations(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $teamLeader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $teamLeader,
            $game
        );

        $registration = $this->createRegistration(
            $tournament,
            $team
        );

        foreach ([$player, $admin] as $user) {
            $this
                ->actingAs($user)
                ->patch(
                    route(
                        'tournaments.registrations.approve',
                        [
                            $tournament,
                            $registration,
                        ]
                    )
                )
                ->assertForbidden();
        }

        $this->assertDatabaseHas(
            'registrations',
            [
                'id' => $registration->id,
                'status' => Registration::STATUS_PENDING,
            ]
        );
    }
}