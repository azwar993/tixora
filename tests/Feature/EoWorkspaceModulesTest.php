<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EoWorkspaceModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_eo_can_open_the_activity_settings_and_help_pages_with_active_sidebar_links(): void
    {
        $eo = $this->user('eo', 'Creator Name');

        $this->actingAs($eo)
            ->get(route('eo.activity.index'))
            ->assertOk()
            ->assertSee('Aktivitas')
            ->assertSee('class="eo-menu-item active" href="' . route('eo.activity.index') . '"', false)
            ->assertSee('Creator')
            ->assertSee('Belum ada aktivitas');

        $this->actingAs($eo)
            ->get(route('eo.settings.edit'))
            ->assertOk()
            ->assertSee('Pengaturan')
            ->assertSee('Creator Name')
            ->assertSee('class="eo-menu-item active" href="' . route('eo.settings.edit') . '"', false);

        $this->actingAs($eo)
            ->get(route('eo.help.index'))
            ->assertOk()
            ->assertSee('Bantuan')
            ->assertSee('Cara membuat event')
            ->assertSee('numbered seating')
            ->assertSee('class="eo-menu-item active" href="' . route('eo.help.index') . '"', false);
    }

    public function test_guest_and_non_eo_users_cannot_open_new_creator_pages(): void
    {
        foreach ([
            route('eo.activity.index'),
            route('eo.settings.edit'),
            route('eo.help.index'),
        ] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        $buyer = $this->user('user', 'Buyer');
        foreach ([
            route('eo.activity.index'),
            route('eo.settings.edit'),
            route('eo.help.index'),
        ] as $url) {
            $this->actingAs($buyer)->get($url)->assertForbidden();
        }
    }

    public function test_activity_is_derived_from_real_records_and_scoped_to_owned_events(): void
    {
        $owner = $this->user('eo', 'Owner');
        $ownedEvent = $this->event($owner, 'Owned Festival');
        $ownedTicket = $this->ticket($ownedEvent, 'Owned VIP');
        $this->order($ownedTicket, 'OWNED-ORDER');

        $otherOwner = $this->user('eo', 'Other Owner');
        $foreignEvent = $this->event($otherOwner, 'Private Festival');
        $foreignTicket = $this->ticket($foreignEvent, 'Private Ticket');
        $this->order($foreignTicket, 'PRIVATE-ORDER');

        $this->actingAs($owner)
            ->get(route('eo.activity.index'))
            ->assertOk()
            ->assertSee('Owned Festival')
            ->assertSee('Owned VIP')
            ->assertSee('OWNED-ORDER')
            ->assertDontSee('Private Festival')
            ->assertDontSee('Private Ticket')
            ->assertDontSee('PRIVATE-ORDER');
    }

    public function test_eo_can_update_only_their_own_name_and_validation_is_applied(): void
    {
        $eo = $this->user('eo', 'Before Name');
        $buyer = $this->user('user', 'Unchanged Buyer');

        $this->actingAs($eo)
            ->patch(route('eo.settings.update'), ['name' => 'Updated Creator'])
            ->assertRedirect(route('eo.settings.edit'))
            ->assertSessionHas('success', 'Nama creator berhasil diperbarui.');

        $this->assertSame('Updated Creator', $eo->fresh()->name);
        $this->assertSame('Unchanged Buyer', $buyer->fresh()->name);

        $this->actingAs($eo)
            ->from(route('eo.settings.edit'))
            ->patch(route('eo.settings.update'), ['name' => ''])
            ->assertRedirect(route('eo.settings.edit'))
            ->assertSessionHasErrors('name');

        $this->assertSame('Updated Creator', $eo->fresh()->name);
    }

    private function user(string $role, string $name): User
    {
        return User::create([
            'name' => $name,
            'email' => $role . '-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function event(User $owner, string $name): Event
    {
        return Event::create([
            'user_id' => $owner->id,
            'name' => $name,
            'category' => 'Music',
            'location' => 'Jakarta',
            'venue' => 'Test Venue',
            'event_date' => today()->addDays(10)->toDateString(),
            'status' => 'coming_soon',
            'approval_status' => 'approved',
            'workflow_status' => 'submitted',
            'description' => 'Creator module test event.',
        ]);
    }

    private function ticket(Event $event, string $name): Ticket
    {
        return Ticket::create([
            'event_id' => $event->id,
            'name' => $name,
            'price' => 50000,
            'quota' => 100,
            'sold' => 0,
        ]);
    }

    private function order(Ticket $ticket, string $code): Order
    {
        return Order::create([
            'order_code' => $code,
            'user_id' => $ticket->event->user_id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'total_price' => 50000,
            'status' => 'pending',
        ]);
    }
}
