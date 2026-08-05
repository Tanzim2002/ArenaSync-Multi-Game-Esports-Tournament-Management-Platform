<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_registration_pages_are_available(): void
    {
        $this->get('/login')
            ->assertOk();

        $this->get('/register')
            ->assertOk();
    }

    public function test_player_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Player',
            'email' => 'testplayer@example.com',
            'role' => User::ROLE_PLAYER,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Test Player',
            'email' => 'testplayer@example.com',
            'role' => User::ROLE_PLAYER,
        ]);
    }

    public function test_organizer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Organizer',
            'email' => 'organizer@example.com',
            'role' => User::ROLE_ORGANIZER,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'organizer@example.com',
            'role' => User::ROLE_ORGANIZER,
        ]);
    }

    public function test_public_registration_cannot_create_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Fake Admin',
            'email' => 'fakeadmin@example.com',
            'role' => User::ROLE_ADMIN,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('role');

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'fakeadmin@example.com',
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_PLAYER,
        ]);

        $response = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'player@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_PLAYER,
        ]);

        $response = $this->post('/login', [
            'email' => 'player@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}