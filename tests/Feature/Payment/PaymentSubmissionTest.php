<?php

namespace Tests\Feature\Payment;

use App\Models\Game;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function createTeam(
        User $leader,
        Game $game
    ): Team {
        return Team::create([
            'leader_id' => $leader->id,
            'name' => 'Payment Test Team',
            'preferred_game_id' => $game->id,
            'description'
                => 'Team created for payment submission testing.',
        ]);
    }

    private function createTournament(
        User $organizer,
        Game $game,
        bool $isPaid = true
    ): Tournament {
        return Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'game_id' => $game->id,
            'is_paid' => $isPaid,
            'entry_fee' => $isPaid
                ? 500
                : 0,
        ]);
    }

    private function createRegistration(
        Tournament $tournament,
        Team $team,
        string $status
    ): Registration {
        return Registration::create([
            'tournament_id'
                => $tournament->id,

            'team_id'
                => $team->id,

            'status'
                => $status,

            'submitted_at'
                => now(),
        ]);
    }

    public function test_team_leader_can_submit_payment_for_approved_registration(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $leader,
            $game
        );

        $registration =
            $this->createRegistration(
                $tournament,
                $team,
                Registration::STATUS_APPROVED
            );

        $response = $this
            ->actingAs($leader)
            ->post(
                route(
                    'registrations.payment.store',
                    $registration
                ),
                [
                    'amount' => 500,
                    'method' => 'bKash',
                    'reference' => 'TXN-000001',
                ]
            );

        $response
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'payments',
            [
                'registration_id'
                    => $registration->id,

                'status'
                    => 'PENDING',
            ]
        );
    }

    public function test_free_tournament_rejects_payment_submission(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game,
            false
        );

        $team = $this->createTeam(
            $leader,
            $game
        );

        $registration =
            $this->createRegistration(
                $tournament,
                $team,
                Registration::STATUS_APPROVED
            );

        $response = $this
            ->actingAs($leader)
            ->post(
                route(
                    'registrations.payment.store',
                    $registration
                ),
                [
                    'amount' => 500,
                    'method' => 'bKash',
                    'reference' => 'TXN-000002',
                ]
            );

        $response->assertSessionHasErrors(
            'payment'
        );

        $this->assertDatabaseMissing(
            'payments',
            [
                'registration_id'
                    => $registration->id,
            ]
        );
    }

    public function test_pending_registration_cannot_submit_payment(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $leader,
            $game
        );

        $registration =
            $this->createRegistration(
                $tournament,
                $team,
                Registration::STATUS_PENDING
            );

        $response = $this
            ->actingAs($leader)
            ->post(
                route(
                    'registrations.payment.store',
                    $registration
                ),
                [
                    'amount' => 500,
                    'method' => 'bKash',
                    'reference' => 'TXN-000003',
                ]
            );

        $response->assertSessionHasErrors(
            'payment'
        );
    }

    public function test_duplicate_payment_submission_is_rejected(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $leader,
            $game
        );

        $registration =
            $this->createRegistration(
                $tournament,
                $team,
                Registration::STATUS_APPROVED
            );

        $this
            ->actingAs($leader)
            ->post(
                route(
                    'registrations.payment.store',
                    $registration
                ),
                [
                    'amount' => 500,
                    'method' => 'bKash',
                    'reference' => 'TXN-000004',
                ]
            );

        $response = $this
            ->actingAs($leader)
            ->post(
                route(
                    'registrations.payment.store',
                    $registration
                ),
                [
                    'amount' => 500,
                    'method' => 'Nagad',
                    'reference' => 'TXN-000005',
                ]
            );

        $response->assertSessionHasErrors(
            'payment'
        );
    }

    public function test_non_leader_cannot_submit_payment(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $leader = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $stranger = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $game = Game::factory()->create();

        $tournament = $this->createTournament(
            $organizer,
            $game
        );

        $team = $this->createTeam(
            $leader,
            $game
        );

        $registration =
            $this->createRegistration(
                $tournament,
                $team,
                Registration::STATUS_APPROVED
            );

        $this
            ->actingAs($stranger)
            ->post(
                route(
                    'registrations.payment.store',
                    $registration
                ),
                [
                    'amount' => 500,
                    'method' => 'bKash',
                    'reference' => 'TXN-000006',
                ]
            )
            ->assertForbidden();
    }
}