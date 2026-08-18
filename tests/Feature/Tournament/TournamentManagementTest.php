<?php

namespace Tests\Feature\Tournament;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Return valid tournament form data.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validTournamentData(
        Game $game,
        array $overrides = []
    ): array {
        return array_merge([
            'game_id' => $game->id,

            'category' => 'Competitive',

            'region' => 'South Asia',

            'prize_type' => 'Cash',

            'title' => 'ArenaSync Championship 2026',

            'description' =>
                'A competitive esports tournament for registered teams and players.',

            'registration_deadline' =>
                now()->addDays(2)->format('Y-m-d H:i:s'),

            'start_at' =>
                now()->addDays(4)->format('Y-m-d H:i:s'),

            'end_at' =>
                now()->addDays(5)->format('Y-m-d H:i:s'),

            'rules' =>
                'All participating teams must follow the official tournament rules.',

            'prize_pool' => 5000,

            'team_limit' => 16,

            'match_format' => 'Single Elimination',
        ], $overrides);
    }

    public function test_guest_is_redirected_from_tournament_creation(): void
    {
        $game = Game::factory()->create();

        $this->post(
            route('tournaments.store'),
            $this->validTournamentData($game)
        )->assertRedirect(route('login'));

        $this->assertDatabaseCount(
            'tournaments',
            0
        );
    }

    public function test_player_and_admin_cannot_create_tournaments(): void
    {
        $game = Game::factory()->create();

        foreach (
            [
                User::ROLE_PLAYER,
                User::ROLE_ADMIN,
            ] as $role
        ) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $this
                ->actingAs($user)
                ->post(
                    route('tournaments.store'),
                    $this->validTournamentData($game)
                )
                ->assertForbidden();
        }

        $this->assertDatabaseCount(
            'tournaments',
            0
        );
    }

    public function test_organizer_can_create_tournament_as_draft(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $data = $this->validTournamentData(
            $game
        );

        $response = $this
            ->actingAs($organizer)
            ->post(
                route('tournaments.store'),
                $data
            );

        $tournament = Tournament::query()
            ->where(
                'title',
                $data['title']
            )
            ->firstOrFail();

        $response
            ->assertRedirect(
                route(
                    'tournaments.show',
                    $tournament
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,
                'organizer_id' => $organizer->id,
                'game_id' => $game->id,
                'category' => 'Competitive',
                'region' => 'South Asia',
                'prize_type' => 'Cash',
                'title' => $data['title'],
                'status' =>
                    Tournament::STATUS_DRAFT,
            ]
        );
    }

    public function test_organizer_cannot_assign_another_organizer_or_status_during_creation(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $otherOrganizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $data = $this->validTournamentData(
            $game,
            [
                'organizer_id' =>
                    $otherOrganizer->id,

                'status' =>
                    Tournament::STATUS_COMPLETED,
            ]
        );

        $this
            ->actingAs($organizer)
            ->post(
                route('tournaments.store'),
                $data
            )
            ->assertRedirect();

        $tournament = Tournament::query()
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(
            $organizer->id,
            $tournament->organizer_id
        );

        $this->assertSame(
            Tournament::STATUS_DRAFT,
            $tournament->status
        );
    }

    public function test_invalid_tournament_information_is_rejected(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $response = $this
            ->actingAs($organizer)
            ->post(
                route('tournaments.store'),
                [
                    'game_id' => $game->id,

                    'category' => '',

                    'region' => '',

                    'prize_type' => '',

                    'title' => '',

                    'description' => 'Too short',

                    'registration_deadline' =>
                        now()
                            ->subDay()
                            ->format('Y-m-d H:i:s'),

                    'start_at' =>
                        now()
                            ->subDays(2)
                            ->format('Y-m-d H:i:s'),

                    'end_at' =>
                        now()
                            ->subDays(3)
                            ->format('Y-m-d H:i:s'),

                    'rules' => 'Short',

                    'prize_pool' => -10,

                    'team_limit' => 1,

                    'match_format' => '',
                ]
            );

        $response->assertSessionHasErrors([
            'category',
            'region',
            'prize_type',
            'title',
            'description',
            'registration_deadline',
            'start_at',
            'end_at',
            'rules',
            'prize_pool',
            'team_limit',
            'match_format',
        ]);

        $this->assertDatabaseCount(
            'tournaments',
            0
        );
    }

    public function test_organizer_can_update_own_draft_tournament(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $newGame = Game::factory()->create();

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'game_id' => $game->id,
            'status' => Tournament::STATUS_DRAFT,
        ]);

        $data = $this->validTournamentData(
            $newGame,
            [
                'category' => 'Professional',
                'region' => 'Asia',
                'prize_type' => 'Cash Prize',
                'title' =>
                    'Updated ArenaSync Championship',
            ]
        );

        $response = $this
            ->actingAs($organizer)
            ->put(
                route(
                    'tournaments.update',
                    $tournament
                ),
                $data
            );

        $response
            ->assertRedirect(
                route(
                    'tournaments.show',
                    $tournament
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,

                'game_id' => $newGame->id,

                'category' => 'Professional',

                'region' => 'Asia',

                'prize_type' => 'Cash Prize',

                'title' =>
                    'Updated ArenaSync Championship',

                'status' =>
                    Tournament::STATUS_DRAFT,
            ]
        );
    }

    public function test_other_organizer_cannot_update_tournament(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $otherOrganizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament = Tournament::factory()->create([
            'organizer_id' => $owner->id,
            'game_id' => $game->id,
            'status' => Tournament::STATUS_DRAFT,
            'title' => 'Original Tournament',
        ]);

        $this
            ->actingAs($otherOrganizer)
            ->put(
                route(
                    'tournaments.update',
                    $tournament
                ),
                $this->validTournamentData(
                    $game,
                    [
                        'title' =>
                            'Unauthorized Update',
                    ]
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,
                'title' => 'Original Tournament',
            ]
        );
    }

    public function test_published_tournament_cannot_be_updated(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,

            'game_id' => $game->id,

            'status' =>
                Tournament::STATUS_REGISTRATION_OPEN,
        ]);

        $this
            ->actingAs($organizer)
            ->put(
                route(
                    'tournaments.update',
                    $tournament
                ),
                $this->validTournamentData(
                    $game
                )
            )
            ->assertForbidden();
    }

    public function test_organizer_can_publish_own_draft_tournament(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,

            'status' =>
                Tournament::STATUS_DRAFT,

            'registration_deadline' =>
                now()->addDays(2),
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.publish',
                    $tournament
                )
            );

        $response
            ->assertRedirect(
                route(
                    'tournaments.show',
                    $tournament
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,

                'status' =>
                    Tournament::STATUS_REGISTRATION_OPEN,
            ]
        );
    }

    public function test_other_organizer_cannot_publish_tournament(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $otherOrganizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $owner->id,

            'status' =>
                Tournament::STATUS_DRAFT,
        ]);

        $this
            ->actingAs($otherOrganizer)
            ->patch(
                route(
                    'tournaments.publish',
                    $tournament
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,

                'status' =>
                    Tournament::STATUS_DRAFT,
            ]
        );
    }

    public function test_organizer_can_cancel_own_tournament(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,

            'status' =>
                Tournament::STATUS_REGISTRATION_OPEN,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.cancel',
                    $tournament
                )
            );

        $response
            ->assertRedirect(
                route(
                    'tournaments.show',
                    $tournament
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,

                'status' =>
                    Tournament::STATUS_CANCELLED,
            ]
        );
    }

    public function test_completed_tournament_cannot_be_cancelled(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,

            'status' =>
                Tournament::STATUS_COMPLETED,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.cancel',
                    $tournament
                )
            );

        $response->assertSessionHasErrors(
            'cancel'
        );

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,

                'status' =>
                    Tournament::STATUS_COMPLETED,
            ]
        );
    }
}