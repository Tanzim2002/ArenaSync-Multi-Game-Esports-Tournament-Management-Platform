<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:ADMIN'])
            ->get('/test/admin-only', function () {
                return response('Admin access granted.');
            });

        Route::middleware(['web', 'auth', 'role:ORGANIZER'])
            ->get('/test/organizer-only', function () {
                return response('Organizer access granted.');
            });
    }

    public function test_guest_is_redirected_from_protected_route(): void
    {
        $this->get('/test/admin-only')
            ->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->get('/test/admin-only')
            ->assertOk()
            ->assertSeeText('Admin access granted.');
    }

    public function test_player_cannot_access_admin_route(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $this->actingAs($player)
            ->get('/test/admin-only')
            ->assertForbidden();
    }

    public function test_organizer_can_access_organizer_route(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $this->actingAs($organizer)
            ->get('/test/organizer-only')
            ->assertOk()
            ->assertSeeText('Organizer access granted.');
    }

    public function test_player_cannot_access_organizer_route(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $this->actingAs($player)
            ->get('/test/organizer-only')
            ->assertForbidden();
    }
}