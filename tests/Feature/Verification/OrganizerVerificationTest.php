<?php

namespace Tests\Feature\Verification;

use App\Models\OrganizerVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizerVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_organizer_verification_page(): void
    {
        $this->get(
            route('organizer-verification.show')
        )->assertRedirect('/login');
    }

    public function test_player_cannot_access_organizer_verification_page(): void
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $this->actingAs($player)
            ->get(route('organizer-verification.show'))
            ->assertForbidden();
    }

    public function test_organizer_can_view_verification_page(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer-verification.show'))
            ->assertOk()
            ->assertSeeText('Organizer Verification')
            ->assertSeeText('Not Yet Requested');
    }

    public function test_organizer_can_submit_verification_request(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $response = $this->actingAs($organizer)
            ->post(route('organizer-verification.store'));

        $response->assertSessionHas(
            'success',
            'Organizer verification request submitted successfully.'
        );

        $this->assertDatabaseHas(
            'organizer_verifications',
            [
                'organizer_id' => $organizer->id,
                'status' => OrganizerVerification::STATUS_PENDING,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );
    }

    public function test_duplicate_pending_request_is_prevented(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($organizer)
            ->post(route('organizer-verification.store'));

        $response->assertSessionHasErrors('verification');

        $this->assertDatabaseCount(
            'organizer_verifications',
            1
        );

        $this->assertDatabaseHas(
            'organizer_verifications',
            [
                'organizer_id' => $organizer->id,
                'status' => OrganizerVerification::STATUS_PENDING,
            ]
        );
    }

    public function test_verified_organizer_cannot_submit_another_request(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_VERIFIED,
            'requested_at' => now()->subDay(),
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($organizer)
            ->post(route('organizer-verification.store'));

        $response->assertSessionHasErrors('verification');

        $this->assertDatabaseCount(
            'organizer_verifications',
            1
        );

        $this->assertDatabaseHas(
            'organizer_verifications',
            [
                'organizer_id' => $organizer->id,
                'status' => OrganizerVerification::STATUS_VERIFIED,
            ]
        );
    }

    public function test_organizer_cannot_access_admin_verification_page(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $this->actingAs($organizer)
            ->get(route('admin.organizer-verifications.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_verification_requests(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $organizer = User::factory()->create([
            'name' => 'Arena Organizer',
            'role' => User::ROLE_ORGANIZER,
        ]);

        OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.organizer-verifications.index'))
            ->assertOk()
            ->assertSeeText('Organizer Verification Requests')
            ->assertSeeText('Arena Organizer')
            ->assertSeeText(OrganizerVerification::STATUS_PENDING);
    }

    public function test_admin_can_approve_pending_verification(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $verification = OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->patch(
                route(
                    'admin.organizer-verifications.approve',
                    $verification
                )
            );

        $response->assertSessionHas(
            'success',
            'Organizer verification approved successfully.'
        );

        $verification->refresh();

        $this->assertSame(
            OrganizerVerification::STATUS_VERIFIED,
            $verification->status
        );

        $this->assertSame(
            $admin->id,
            $verification->reviewed_by
        );

        $this->assertNotNull(
            $verification->reviewed_at
        );
    }

    public function test_admin_can_reject_pending_verification(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $verification = OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->patch(
                route(
                    'admin.organizer-verifications.reject',
                    $verification
                )
            );

        $response->assertSessionHas(
            'success',
            'Organizer verification rejected successfully.'
        );

        $verification->refresh();

        $this->assertSame(
            OrganizerVerification::STATUS_REJECTED,
            $verification->status
        );

        $this->assertSame(
            $admin->id,
            $verification->reviewed_by
        );

        $this->assertNotNull(
            $verification->reviewed_at
        );
    }

    public function test_decided_verification_cannot_be_decided_again(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $verification = OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_VERIFIED,
            'requested_at' => now()->subDay(),
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->patch(
                route(
                    'admin.organizer-verifications.reject',
                    $verification
                )
            );

        $response->assertSessionHasErrors('verification');

        $this->assertDatabaseHas(
            'organizer_verifications',
            [
                'id' => $verification->id,
                'status' => OrganizerVerification::STATUS_VERIFIED,
                'reviewed_by' => $admin->id,
            ]
        );
    }

    public function test_rejected_organizer_can_resubmit_request(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        $verification = OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_REJECTED,
            'requested_at' => now()->subDays(2),
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($organizer)
            ->post(route('organizer-verification.store'));

        $response->assertSessionHas(
            'success',
            'Organizer verification request resubmitted successfully.'
        );

        $verification->refresh();

        $this->assertSame(
            OrganizerVerification::STATUS_PENDING,
            $verification->status
        );

        $this->assertNull(
            $verification->reviewed_by
        );

        $this->assertNull(
            $verification->reviewed_at
        );
    }

    public function test_verified_badge_is_visible_for_verified_organizer(): void
    {
        $organizer = User::factory()->create([
            'name' => 'Verified Arena Organizer',
            'role' => User::ROLE_ORGANIZER,
        ]);

        OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_VERIFIED,
            'requested_at' => now()->subDay(),
            'reviewed_at' => now(),
        ]);

        $this->actingAs($organizer)
            ->get('/')
            ->assertOk()
            ->assertSeeText('Verified Arena Organizer')
            ->assertSeeText('✓ Verified');
    }

    public function test_pending_organizer_does_not_receive_verified_badge(): void
    {
        $organizer = User::factory()->create([
            'role' => User::ROLE_ORGANIZER,
        ]);

        OrganizerVerification::query()->create([
            'organizer_id' => $organizer->id,
            'status' => OrganizerVerification::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->actingAs($organizer)
            ->get('/')
            ->assertOk()
            ->assertDontSeeText('✓ Verified');
    }
}
