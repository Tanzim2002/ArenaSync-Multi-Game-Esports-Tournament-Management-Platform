<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Return valid game form data.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validGameData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Valorant',
            'genre' => 'Tactical Shooter',
            'platform' => 'PC',
            'rules' => 'Each team must have five active players during every competitive match.',
            'team_size' => 5,
        ], $overrides);
    }

    public function test_public_user_can_view_game_list_and_details(): void
    {
        $game = Game::factory()->create([
            'name' => 'Valorant',
        ]);

        $this->get(route('games.index'))
            ->assertOk()
            ->assertSeeText('Valorant');

        $this->get(route('games.show', $game))
            ->assertOk()
            ->assertSeeText($game->name)
            ->assertSeeText($game->genre)
            ->assertSeeText($game->rules);
    }

    public function test_guest_is_redirected_from_game_creation_page(): void
    {
        $this->get(route('games.create'))
            ->assertRedirect(route('login'));
    }

    public function test_player_and_organizer_cannot_manage_games(): void
    {
        foreach ([User::ROLE_PLAYER, User::ROLE_ORGANIZER] as $role) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $this->actingAs($user)
                ->get(route('games.create'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('games.store'), $this->validGameData())
                ->assertForbidden();
        }

        $this->assertDatabaseCount('games', 0);
    }

    public function test_admin_can_create_a_game(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $data = $this->validGameData();

        $response = $this
            ->actingAs($admin)
            ->post(route('games.store'), $data);

        $game = Game::query()
            ->where('name', 'Valorant')
            ->firstOrFail();

        $response
            ->assertRedirect(route('games.show', $game))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('games', $data);
    }

    public function test_invalid_game_information_is_rejected(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('games.store'), [
                'name' => '',
                'genre' => '',
                'platform' => '',
                'rules' => 'Short',
                'team_size' => 0,
            ]);

        $response->assertSessionHasErrors([
            'name',
            'genre',
            'platform',
            'rules',
            'team_size',
        ]);

        $this->assertDatabaseCount('games', 0);
    }

    public function test_duplicate_game_name_is_rejected(): void
    {
        Game::factory()->create([
            'name' => 'Valorant',
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('games.store'), $this->validGameData());

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('games', 1);
    }

    public function test_admin_can_update_a_game(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $game = Game::factory()->create([
            'name' => 'Old Game Name',
        ]);

        $data = $this->validGameData([
            'name' => 'Counter-Strike 2',
            'genre' => 'Competitive Shooter',
            'team_size' => 5,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('games.update', $game), $data);

        $response
            ->assertRedirect(route('games.show', $game))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'name' => 'Counter-Strike 2',
            'genre' => 'Competitive Shooter',
            'team_size' => 5,
        ]);

        $this->assertDatabaseMissing('games', [
            'id' => $game->id,
            'name' => 'Old Game Name',
        ]);
    }

    public function test_admin_can_delete_a_game(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $game = Game::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->delete(route('games.destroy', $game));

        $response
            ->assertRedirect(route('games.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('games', [
            'id' => $game->id,
        ]);
    }
}