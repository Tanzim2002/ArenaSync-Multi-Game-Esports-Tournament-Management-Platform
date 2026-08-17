<?php

namespace Tests\Feature\Registration;

use App\Models\Game;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a player-owned team for the supplied game.
     */
    private function createTeam(
        User $leader,
        Game $game,
        string $name = 'Arena Test Team'
    ): Team {
        return Team::create([
            'leader_id' => $leader->id,
            'name' => $name,
            'preferred_game_id' => $game->id,
            'description' => 'Team created for tournament registration testing.',
        ]);
    }

    /**
     * Create a tournament with registration open by default.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function createTournament(
        Game $game,
        array $overrides = []
    ): Tournament {
        return Tournament::factory()->create(
            array_merge([
                'game_id' => $game->id,
                'status' => Tournament::STATUS_REGISTRATION_OPEN,
                'registration_deadline' => now()->addDays(3),
                'team_limit' => 8,
            ], $overrides)
        );
    }

    public function test_guest_is_redirected_from_tournament_registration_page(): void
    {
        $game = Game::factory()->create();

        $tournament = $this->createTournament($game);

        $this->get(
            route('tournaments.registrations.create', $tournament)
        )->assertRedirect(route('login'));
    }

    public function test_team_leader_can_view_tournament_registration_page(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $team = $this->createTeam(
            $player,
            $game
        );

        $tournament = $this->createTournament($game);

        $response = $this
            ->actingAs($player)
            ->get(
                route(
                    'tournaments.registrations.create',
                    $tournament
                )
            );

        $response
            ->assertOk()
            ->assertSeeText('Tournament Registration')
            ->assertSeeText($team->name);
    }

    public function test_team_leader_can_submit_valid_tournament_registration(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $team = $this->createTeam(
            $player,
            $game
        );

        $tournament = $this->createTournament($game);

        $response = $this
            ->actingAs($player)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $team->id,
                ]
            );

        $response
            ->assertRedirect(
                route(
                    'tournaments.show',
                    $tournament
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas('registrations', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => Registration::STATUS_PENDING,
        ]);
    }

    public function test_player_cannot_register_a_team_they_do_not_lead(): void
    {
        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $otherPlayer = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $team = $this->createTeam(
            $leader,
            $game
        );

        $tournament = $this->createTournament($game);

        $this
            ->actingAs($otherPlayer)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $team->id,
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseCount(
            'registrations',
            0
        );
    }

    public function test_organizer_and_admin_cannot_submit_team_registration(): void
    {
        $game = Game::factory()->create();

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $team = $this->createTeam(
            $player,
            $game
        );

        $tournament = $this->createTournament($game);

        foreach (
            [
                User::ROLE_ORGANIZER,
                User::ROLE_ADMIN,
            ] as $role
        ) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $this
                ->actingAs($user)
                ->post(
                    route(
                        'tournaments.registrations.store',
                        $tournament
                    ),
                    [
                        'team_id' => $team->id,
                    ]
                )
                ->assertForbidden();
        }

        $this->assertDatabaseCount(
            'registrations',
            0
        );
    }

    public function test_registration_is_rejected_when_tournament_is_not_open(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $team = $this->createTeam(
            $player,
            $game
        );

        $tournament = $this->createTournament(
            $game,
            [
                'status' => Tournament::STATUS_DRAFT,
            ]
        );

        $response = $this
            ->actingAs($player)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $team->id,
                ]
            );

        $response->assertSessionHasErrors(
            'registration'
        );

        $this->assertDatabaseCount(
            'registrations',
            0
        );
    }

    public function test_registration_is_rejected_after_deadline(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $team = $this->createTeam(
            $player,
            $game
        );

        $tournament = $this->createTournament(
            $game,
            [
                'registration_deadline' => now()->subMinute(),
            ]
        );

        $response = $this
            ->actingAs($player)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $team->id,
                ]
            );

        $response->assertSessionHasErrors(
            'registration'
        );

        $this->assertDatabaseCount(
            'registrations',
            0
        );
    }

    public function test_duplicate_team_registration_is_rejected(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $team = $this->createTeam(
            $player,
            $game
        );

        $tournament = $this->createTournament($game);

        Registration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => Registration::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $response = $this
            ->actingAs($player)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $team->id,
                ]
            );

        $response->assertSessionHasErrors(
            'team_id'
        );

        $this->assertDatabaseCount(
            'registrations',
            1
        );
    }

    public function test_team_for_different_game_cannot_register(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $tournamentGame = Game::factory()->create();

        $otherGame = Game::factory()->create();

        $team = $this->createTeam(
            $player,
            $otherGame
        );

        $tournament = $this->createTournament(
            $tournamentGame
        );

        $response = $this
            ->actingAs($player)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $team->id,
                ]
            );

        $response->assertSessionHasErrors(
            'team_id'
        );

        $this->assertDatabaseCount(
            'registrations',
            0
        );
    }

    public function test_registration_is_rejected_when_tournament_is_full(): void
    {
        $game = Game::factory()->create();

        $existingLeader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $existingTeam = $this->createTeam(
            $existingLeader,
            $game,
            'Existing Registered Team'
        );

        $newLeader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $newTeam = $this->createTeam(
            $newLeader,
            $game,
            'New Registration Team'
        );

        $tournament = $this->createTournament(
            $game,
            [
                'team_limit' => 1,
            ]
        );

        Registration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $existingTeam->id,
            'status' => Registration::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $response = $this
            ->actingAs($newLeader)
            ->post(
                route(
                    'tournaments.registrations.store',
                    $tournament
                ),
                [
                    'team_id' => $newTeam->id,
                ]
            );

        $response->assertSessionHasErrors(
            'registration'
        );

        $this->assertDatabaseCount(
            'registrations',
            1
        );

        $this->assertDatabaseMissing(
            'registrations',
            [
                'tournament_id' => $tournament->id,
                'team_id' => $newTeam->id,
            ]
        );
    }
}