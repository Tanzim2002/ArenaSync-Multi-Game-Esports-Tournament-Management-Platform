<?php

namespace Tests\Feature\Payment;

use App\Models\Game;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingPayment(User $organizer, User $leader, Game $game): Payment
    {
        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'game_id' => $game->id,
            'is_paid' => true,
            'entry_fee' => 500,
        ]);

        $team = Team::create([
            'leader_id' => $leader->id,
            'name' => 'Verification Test Team',
            'preferred_game_id' => $game->id,
        ]);

        $registration = Registration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => Registration::STATUS_APPROVED,
            'submitted_at' => now(),
        ]);

        return Payment::create([
            'registration_id' => $registration->id,
            'amount' => 500,
            'method' => 'bKash',
            'reference' => 'TXN-VERIFY-'.$registration->id,
            'status' => Payment::STATUS_PENDING,
            'submitted_at' => now(),
        ]);
    }

    public function test_owner_organizer_can_verify_pending_payment(): void
    {
        $organizer = User::factory()->create(['role' => User::ROLE_ORGANIZER]);
        $leader = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $game = Game::factory()->create();
        $payment = $this->createPendingPayment($organizer, $leader, $game);
        $tournament = $payment->registration->tournament;

        $response = $this
            ->actingAs($organizer)
            ->patch(route('tournaments.payments.verify', [$tournament, $payment]));

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'VERIFIED',
            'reviewed_by' => $organizer->id,
        ]);
    }

    public function test_owner_organizer_can_reject_pending_payment(): void
    {
        $organizer = User::factory()->create(['role' => User::ROLE_ORGANIZER]);
        $leader = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $game = Game::factory()->create();
        $payment = $this->createPendingPayment($organizer, $leader, $game);
        $tournament = $payment->registration->tournament;

        $response = $this
            ->actingAs($organizer)
            ->patch(route('tournaments.payments.reject', [$tournament, $payment]));

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'REJECTED']);
    }

    public function test_already_reviewed_payment_cannot_be_reviewed_again(): void
    {
        $organizer = User::factory()->create(['role' => User::ROLE_ORGANIZER]);
        $leader = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $game = Game::factory()->create();
        $payment = $this->createPendingPayment($organizer, $leader, $game);
        $tournament = $payment->registration->tournament;
        $payment->update(['status' => Payment::STATUS_VERIFIED]);

        $response = $this
            ->actingAs($organizer)
            ->patch(route('tournaments.payments.reject', [$tournament, $payment]));

        $response->assertSessionHasErrors('payment');
    }

    public function test_other_organizer_cannot_review_payment(): void
    {
        $organizer = User::factory()->create(['role' => User::ROLE_ORGANIZER]);
        $otherOrganizer = User::factory()->create(['role' => User::ROLE_ORGANIZER]);
        $leader = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $game = Game::factory()->create();
        $payment = $this->createPendingPayment($organizer, $leader, $game);
        $tournament = $payment->registration->tournament;

        $this
            ->actingAs($otherOrganizer)
            ->patch(route('tournaments.payments.verify', [$tournament, $payment]))
            ->assertForbidden();
    }

    public function test_player_cannot_review_payment(): void
    {
        $organizer = User::factory()->create(['role' => User::ROLE_ORGANIZER]);
        $leader = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $game = Game::factory()->create();
        $payment = $this->createPendingPayment($organizer, $leader, $game);
        $tournament = $payment->registration->tournament;

        $this
            ->actingAs($leader)
            ->patch(route('tournaments.payments.verify', [$tournament, $payment]))
            ->assertForbidden();
    }
}