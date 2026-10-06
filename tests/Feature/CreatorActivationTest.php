<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatorActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_activate_creator(): void
    {
        $this->post(route('creator.activate'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_activate_creator_and_role_persists(): void
    {
        $user = $this->user('user');

        $this->actingAs($user)
            ->post(route('creator.activate'), ['role' => 'admin'])
            ->assertRedirect(route('eo.dashboard'))
            ->assertSessionHas('success', 'Selamat! Akun kamu sekarang aktif sebagai Event Creator.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'eo',
        ]);

        $this->actingAs($user->fresh())
            ->withSession(['success' => 'Selamat! Akun kamu sekarang aktif sebagai Event Creator.'])
            ->get(route('eo.dashboard'))
            ->assertOk()
            ->assertSee('Selamat! Akun kamu sekarang aktif sebagai Event Creator.');
    }

    public function test_activation_is_idempotent_for_eo(): void
    {
        $eo = $this->user('eo');

        $this->actingAs($eo)
            ->post(route('creator.activate'), ['role' => 'admin'])
            ->assertRedirect(route('eo.dashboard'));

        $this->assertSame('eo', $eo->fresh()->role);
    }

    public function test_admin_cannot_activate_and_role_is_unchanged(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)
            ->post(route('creator.activate'), ['role' => 'eo'])
            ->assertForbidden();

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_buyer_cannot_access_creator_pages_before_activation(): void
    {
        $user = $this->user('user');

        $this->actingAs($user)->get(route('eo.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('eo.events.index'))->assertForbidden();
    }

    public function test_activated_eo_can_access_buyer_and_creator_dashboards_and_events(): void
    {
        $eo = $this->user('eo');

        $this->actingAs($eo)->get(route('eo.dashboard'))->assertOk();
        $this->actingAs($eo)->get(route('eo.events.index'))->assertOk();
        $this->actingAs($eo)->get(route('dashboard'))->assertOk();
    }

    public function test_dashboard_cta_and_account_dropdown_follow_creator_role(): void
    {
        $buyer = $this->user('user');
        $this->actingAs($buyer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Mulai Jadi Event Creator')
            ->assertSee(route('creator.activate'))
            ->assertDontSee('Segera')
            ->assertDontSee('Creator Dashboard');

        $eo = $this->user('eo');
        $this->actingAs($eo)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kamu sudah aktif sebagai Event Creator.')
            ->assertSee('Masuk ke Creator Dashboard')
            ->assertSee(route('eo.dashboard'))
            ->assertSee('Event Saya')
            ->assertSee(route('eo.events.index'))
            ->assertSee('Tiket Saya')
            ->assertSee('Pesanan Saya');
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Activation ' . ucfirst($role),
            'email' => $role . '-' . uniqid() . '@activation.test',
            'password' => 'password',
            'role' => $role,
            'email_verified_at' => now(),
        ]);
    }
}
