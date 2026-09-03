<?php

namespace Tests\Feature\Payments;

use App\Models\Game;
use App\Models\Livestream;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivestreamTest extends TestCase
{
    use RefreshDatabase;

    protected function makeTournament(User $organizer): Tournament
    {
        $game = Game::factory()->create();
        return Tournament::factory()->create(['organizer_id' => $organizer->id, 'game_id' => $game->id]);
    }

    public function test_organizer_can_add_valid_youtube_link(): void
    {
        $organizer  = User::factory()->create(['role' => 'organizer']);
        $tournament = $this->makeTournament($organizer);

        $response = $this->actingAs($organizer)->post(
            route('tournaments.livestreams.store', $tournament),
            ['platform' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=abc123', 'label' => 'Grand Final']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('livestreams', ['tournament_id' => $tournament->id, 'platform' => 'youtube']);
    }

    public function test_mismatched_url_and_platform_is_rejected(): void
    {
        $organizer  = User::factory()->create(['role' => 'organizer']);
        $tournament = $this->makeTournament($organizer);

        $response = $this->actingAs($organizer)->post(
            route('tournaments.livestreams.store', $tournament),
            ['platform' => 'twitch', 'url' => 'https://www.youtube.com/watch?v=abc123']
        );

        $response->assertSessionHasErrors('url');
    }

    public function test_non_organizer_cannot_add_livestream(): void
    {
        $organizer  = User::factory()->create(['role' => 'organizer']);
        $stranger   = User::factory()->create(['role' => 'organizer']);
        $tournament = $this->makeTournament($organizer);

        $response = $this->actingAs($stranger)->post(
            route('tournaments.livestreams.store', $tournament),
            ['platform' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=xyz']
        );

        $response->assertForbidden();
    }

    public function test_guest_can_view_active_livestreams(): void
    {
        $organizer  = User::factory()->create(['role' => 'organizer']);
        $tournament = $this->makeTournament($organizer);
        Livestream::factory()->create(['tournament_id' => $tournament->id, 'is_active' => true]);

        $response = $this->get(route('tournaments.livestreams.index', $tournament));
        $response->assertOk();
    }
}