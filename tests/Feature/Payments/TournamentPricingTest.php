<?php

namespace Tests\Feature\Payments;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentPricingTest extends TestCase
{
    use RefreshDatabase;

    private function makeTournament(
        User $organizer
    ): Tournament {
        $game = Game::factory()->create();

        return Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'game_id' => $game->id,
            'is_paid' => false,
            'entry_fee' => 0,
        ]);
    }

    public function test_organizer_can_mark_tournament_as_paid_with_valid_fee(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = $this->makeTournament(
            $organizer
        );

        $response = $this
            ->actingAs($organizer)
            ->put(
                route(
                    'tournaments.pricing.update',
                    $tournament
                ),
                [
                    'is_paid' => 1,
                    'entry_fee' => 500,
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,
                'is_paid' => 1,
                'entry_fee' => 500,
            ]
        );
    }

    public function test_paid_tournament_requires_entry_fee(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = $this->makeTournament(
            $organizer
        );

        $response = $this
            ->actingAs($organizer)
            ->put(
                route(
                    'tournaments.pricing.update',
                    $tournament
                ),
                [
                    'is_paid' => 1,
                ]
            );

        $response->assertSessionHasErrors(
            'entry_fee'
        );
    }

    public function test_marking_free_forces_entry_fee_to_zero(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = $this->makeTournament(
            $organizer
        );

        $tournament->update([
            'is_paid' => true,
            'entry_fee' => 300,
        ]);

        $this
            ->actingAs($organizer)
            ->put(
                route(
                    'tournaments.pricing.update',
                    $tournament
                ),
                [
                    'is_paid' => 0,
                ]
            );

        $this->assertDatabaseHas(
            'tournaments',
            [
                'id' => $tournament->id,
                'is_paid' => 0,
                'entry_fee' => 0,
            ]
        );
    }

    public function test_non_organizer_cannot_change_pricing(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $stranger = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = $this->makeTournament(
            $organizer
        );

        $this
            ->actingAs($stranger)
            ->put(
                route(
                    'tournaments.pricing.update',
                    $tournament
                ),
                [
                    'is_paid' => 1,
                    'entry_fee' => 200,
                ]
            )
            ->assertForbidden();
    }
}