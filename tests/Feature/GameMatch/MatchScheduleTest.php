<?php

namespace Tests\Feature\GameMatch;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function createTournament(
        User $organizer,
        Game $game,
        bool $isPaid = false
    ): Tournament {
        return Tournament::factory()->create([
            'organizer_id'
                => $organizer->id,

            'game_id'
                => $game->id,

            'is_paid'
                => $isPaid,

            'entry_fee'
                => $isPaid
                    ? 500
                    : 0,

            'start_at'
                => now()->subDay(),

            'end_at'
                => now()->addDays(14),
        ]);
    }

    private function createEligibleTeam(
        Tournament $tournament,
        Game $game,
        string $name
    ): Team {
        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $team = Team::create([
            'leader_id'
                => $leader->id,

            'name'
                => $name,

            'preferred_game_id'
                => $game->id,
        ]);

        $registration =
            Registration::create([
                'tournament_id'
                    => $tournament->id,

                'team_id'
                    => $team->id,

                'status'
                    => Registration::STATUS_APPROVED,

                'submitted_at'
                    => now(),
            ]);

        if ($tournament->is_paid) {
            Payment::create([
                'registration_id'
                    => $registration->id,

                'amount'
                    => $tournament->entry_fee,

                'method'
                    => 'bKash',

                'reference'
                    => 'TXN-'.$registration->id,

                'status'
                    => Payment::STATUS_VERIFIED,

                'submitted_at'
                    => now(),
            ]);
        }

        return $team;
    }

    public function test_organizer_can_schedule_match_between_eligible_teams(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament =
            $this->createTournament(
                $organizer,
                $game
            );

        $teamOne =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Nova'
            );

        $teamTwo =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Eclipse'
            );

        $response = $this
            ->actingAs($organizer)
            ->post(
                route(
                    'tournaments.matches.store',
                    $tournament
                ),
                [
                    'round'
                        => 'Quarterfinal',

                    'team_one_id'
                        => $teamOne->id,

                    'team_two_id'
                        => $teamTwo->id,

                    'scheduled_at'
                        => now()
                            ->addDays(2)
                            ->format(
                                'Y-m-d H:i:s'
                            ),
                ]
            );

        $response
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'matches',
            [
                'tournament_id'
                    => $tournament->id,

                'team_one_id'
                    => $teamOne->id,

                'status'
                    => GameMatch::STATUS_SCHEDULED,
            ]
        );
    }

    public function test_team_without_verified_payment_cannot_be_scheduled(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament =
            $this->createTournament(
                $organizer,
                $game,
                true
            );

        $teamOne =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Paid Team Nova'
            );

        $leaderTwo =
            User::factory()->create([
                'role'
                    => User::ROLE_PLAYER,
            ]);

        $teamTwo = Team::create([
            'leader_id'
                => $leaderTwo->id,

            'name'
                => 'Unpaid Team Eclipse',

            'preferred_game_id'
                => $game->id,
        ]);

        Registration::create([
            'tournament_id'
                => $tournament->id,

            'team_id'
                => $teamTwo->id,

            'status'
                => Registration::STATUS_APPROVED,

            'submitted_at'
                => now(),
        ]);

        $response = $this
            ->actingAs($organizer)
            ->post(
                route(
                    'tournaments.matches.store',
                    $tournament
                ),
                [
                    'round'
                        => 'Quarterfinal',

                    'team_one_id'
                        => $teamOne->id,

                    'team_two_id'
                        => $teamTwo->id,

                    'scheduled_at'
                        => now()
                            ->addDays(2)
                            ->format(
                                'Y-m-d H:i:s'
                            ),
                ]
            );

        $response->assertSessionHasErrors(
            'team_two_id'
        );

        $this->assertDatabaseMissing(
            'matches',
            [
                'tournament_id'
                    => $tournament->id,

                'team_two_id'
                    => $teamTwo->id,
            ]
        );
    }

    public function test_non_organizer_cannot_schedule_match(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $stranger = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament =
            $this->createTournament(
                $organizer,
                $game
            );

        $teamOne =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Nova'
            );

        $teamTwo =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Eclipse'
            );

        $this
            ->actingAs($stranger)
            ->post(
                route(
                    'tournaments.matches.store',
                    $tournament
                ),
                [
                    'round'
                        => 'Quarterfinal',

                    'team_one_id'
                        => $teamOne->id,

                    'team_two_id'
                        => $teamTwo->id,

                    'scheduled_at'
                        => now()->addDays(2),
                ]
            )
            ->assertForbidden();
    }

    public function test_overlapping_schedule_for_same_team_is_rejected(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament =
            $this->createTournament(
                $organizer,
                $game
            );

        $teamOne =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Nova'
            );

        $teamTwo =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Eclipse'
            );

        $teamThree =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Vertex'
            );

        $time = now()
            ->addDays(2)
            ->format(
                'Y-m-d H:i:s'
            );

        $this
            ->actingAs($organizer)
            ->post(
                route(
                    'tournaments.matches.store',
                    $tournament
                ),
                [
                    'round'
                        => 'Round 1',

                    'team_one_id'
                        => $teamOne->id,

                    'team_two_id'
                        => $teamTwo->id,

                    'scheduled_at'
                        => $time,
                ]
            );

        $response = $this
            ->actingAs($organizer)
            ->post(
                route(
                    'tournaments.matches.store',
                    $tournament
                ),
                [
                    'round'
                        => 'Round 2',

                    'team_one_id'
                        => $teamOne->id,

                    'team_two_id'
                        => $teamThree->id,

                    'scheduled_at'
                        => $time,
                ]
            );

        $response->assertSessionHasErrors(
            'scheduled_at'
        );
    }

    public function test_match_status_transition_persists(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $game = Game::factory()->create();

        $tournament =
            $this->createTournament(
                $organizer,
                $game
            );

        $teamOne =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Nova'
            );

        $teamTwo =
            $this->createEligibleTeam(
                $tournament,
                $game,
                'Team Eclipse'
            );

        $match = GameMatch::create([
            'tournament_id'
                => $tournament->id,

            'round'
                => 'Round 1',

            'team_one_id'
                => $teamOne->id,

            'team_two_id'
                => $teamTwo->id,

            'scheduled_at'
                => now()->addDay(),

            'status'
                => GameMatch::STATUS_SCHEDULED,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'matches.status.update',
                    $match
                ),
                [
                    'status'
                        => GameMatch::STATUS_ONGOING,
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'matches',
            [
                'id'
                    => $match->id,

                'status'
                    => GameMatch::STATUS_ONGOING,
            ]
        );
    }
}