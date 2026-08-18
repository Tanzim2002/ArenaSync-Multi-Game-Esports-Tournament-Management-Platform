<?php

namespace Tests\Feature\Tournament;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TournamentStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_timeline_phase_is_derived_from_tournament_dates(): void
    {
        Carbon::setTestNow(
            Carbon::parse('2026-08-18 12:00:00')
        );

        $upcoming = Tournament::factory()->create([
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
        ]);

        $ongoing = Tournament::factory()->create([
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
        ]);

        $completed = Tournament::factory()->create([
            'start_at' => now()->subDays(2),
            'end_at' => now()->subDay(),
        ]);

        $this->assertSame(
            Tournament::PHASE_UPCOMING,
            $upcoming->timelinePhase()
        );

        $this->assertSame(
            Tournament::PHASE_ONGOING,
            $ongoing->timelinePhase()
        );

        $this->assertSame(
            Tournament::PHASE_COMPLETED,
            $completed->timelinePhase()
        );
    }

    public function test_organizer_can_close_registration_from_registration_open(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => Tournament::STATUS_REGISTRATION_OPEN,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_REGISTRATION_CLOSED,
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

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' =>
                Tournament::STATUS_REGISTRATION_CLOSED,
        ]);
    }

    public function test_organizer_can_move_closed_registration_to_ongoing(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'status' =>
                Tournament::STATUS_REGISTRATION_CLOSED,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_ONGOING,
                ]
            );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' => Tournament::STATUS_ONGOING,
        ]);
    }

    public function test_organizer_can_complete_ongoing_tournament(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => Tournament::STATUS_ONGOING,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_COMPLETED,
                ]
            );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' => Tournament::STATUS_COMPLETED,
        ]);
    }

    public function test_invalid_status_jump_is_rejected(): void
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
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_COMPLETED,
                ]
            );

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' =>
                Tournament::STATUS_REGISTRATION_OPEN,
        ]);
    }

    public function test_other_organizer_cannot_change_tournament_status(): void
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
                Tournament::STATUS_REGISTRATION_OPEN,
        ]);

        $this
            ->actingAs($otherOrganizer)
            ->patch(
                route(
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_REGISTRATION_CLOSED,
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' =>
                Tournament::STATUS_REGISTRATION_OPEN,
        ]);
    }

    public function test_completed_tournament_is_terminal(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => Tournament::STATUS_COMPLETED,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_REGISTRATION_CLOSED,
                ]
            );

        $response->assertSessionHasErrors('status');

        $this->assertSame(
            [],
            $tournament
                ->fresh()
                ->allowedStatusTransitions()
        );

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' => Tournament::STATUS_COMPLETED,
        ]);
    }

    public function test_cancelled_tournament_is_terminal(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => Tournament::STATUS_CANCELLED,
        ]);

        $response = $this
            ->actingAs($organizer)
            ->patch(
                route(
                    'tournaments.status.update',
                    $tournament
                ),
                [
                    'status' =>
                        Tournament::STATUS_ONGOING,
                ]
            );

        $response->assertSessionHasErrors('status');

        $this->assertSame(
            [],
            $tournament
                ->fresh()
                ->allowedStatusTransitions()
        );

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'status' => Tournament::STATUS_CANCELLED,
        ]);
    }
}