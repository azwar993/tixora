<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EoTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_eo_tickets(): void
    {
        $event = $this->event($this->user('eo'));

        $this->get(route('eo.events.tickets.index', $event))
            ->assertRedirect(route('login'));
    }

    public function test_buyer_cannot_access_eo_tickets(): void
    {
        $event = $this->event($this->user('eo'));

        $this->actingAs($this->user('user'))
            ->get(route('eo.events.tickets.index', $event))
            ->assertForbidden();
    }

    public function test_eo_can_list_tickets_for_own_event(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event);

        $this->actingAs($owner)
            ->get(route('eo.events.tickets.index', $event))
            ->assertOk()
            ->assertSee($ticket->name)
            ->assertSee('Tersedia');
    }

    public function test_eo_cannot_access_another_owners_event_tickets(): void
    {
        $event = $this->event($this->user('eo'));

        $this->actingAs($this->user('eo'))
            ->get(route('eo.events.tickets.index', $event))
            ->assertNotFound();
    }

    public function test_eo_can_create_ticket_for_owned_draft_event(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);

        $this->actingAs($owner)
            ->post(route('eo.events.tickets.store', $event), $this->ticketData())
            ->assertRedirect(route('eo.events.tickets.index', $event))
            ->assertSessionHas('success', 'Ticket berhasil ditambahkan.');

        $this->assertDatabaseHas('tickets', [
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => '250000.00',
            'quota' => 100,
            'sold' => 0,
            'reserved' => 0,
        ]);
    }

    public function test_eo_can_create_arbitrary_ticket_names_per_event_without_a_predefined_list(): void
    {
        $owner = $this->user('eo');
        $eventA = $this->event($owner, ['name' => 'Event A']);
        $eventB = $this->event($owner, ['name' => 'Event B']);
        $ticketNameA = 'VIP East - Balcony';
        $ticketNameB = 'Student Access / Early Entry';

        $this->actingAs($owner)
            ->get(route('eo.events.tickets.create', $eventA))
            ->assertOk()
            ->assertSee('name="name" type="text"', false)
            ->assertDontSee('<select', false);

        $this->actingAs($owner)
            ->post(route('eo.events.tickets.store', $eventA), $this->ticketData([
                'name' => $ticketNameA,
            ]))
            ->assertRedirect(route('eo.events.tickets.index', $eventA));

        $this->post(route('eo.events.tickets.store', $eventB), $this->ticketData([
            'name' => $ticketNameB,
        ]))->assertRedirect(route('eo.events.tickets.index', $eventB));

        $this->assertDatabaseHas('tickets', [
            'event_id' => $eventA->id,
            'name' => $ticketNameA,
        ]);
        $this->assertDatabaseHas('tickets', [
            'event_id' => $eventB->id,
            'name' => $ticketNameB,
        ]);
        $this->assertSame([$ticketNameA], $eventA->tickets()->pluck('name')->all());
        $this->assertSame([$ticketNameB], $eventB->tickets()->pluck('name')->all());
    }

    public function test_general_admission_public_detail_shows_only_its_real_ticket_types_without_seats(): void
    {
        $owner = $this->user('eo');
        $eventA = $this->event($owner, ['name' => 'Public Event A']);
        $eventB = $this->event($owner, ['name' => 'Public Event B']);

        $this->actingAs($owner)
            ->post(route('eo.events.tickets.store', $eventA), $this->ticketData([
                'name' => 'Balcony East',
            ]))
            ->assertRedirect(route('eo.events.tickets.index', $eventA));
        $this->post(route('eo.events.tickets.store', $eventB), $this->ticketData([
            'name' => 'Student Floor',
        ]))->assertRedirect(route('eo.events.tickets.index', $eventB));

        $eventA->update(['workflow_status' => 'submitted', 'approval_status' => 'approved']);
        $eventB->update(['workflow_status' => 'submitted', 'approval_status' => 'approved']);

        $this->get(route('events.show', $eventA))
            ->assertOk()
            ->assertSee('Balcony East')
            ->assertSee('data-ticket-remaining="100"', false)
            ->assertDontSee('Student Floor')
            ->assertDontSee('id="seatGrid"', false);

        $this->assertSame('general_admission', $eventA->seating_type);
        $this->assertDatabaseCount('event_sections', 0);
        $this->assertDatabaseCount('seats', 0);
    }

    public function test_ticket_creation_ignores_spoofed_event_sold_and_reserved_fields(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $otherEvent = $this->event($this->user('eo'));

        $this->actingAs($owner)
            ->post(route('eo.events.tickets.store', $event), [
                ...$this->ticketData(),
                'event_id' => $otherEvent->id,
                'sold' => 90,
                'reserved' => 10,
            ])
            ->assertRedirect(route('eo.events.tickets.index', $event));

        $ticket = Ticket::query()->where('name', 'VIP')->firstOrFail();
        $this->assertSame($event->id, $ticket->event_id);
        $this->assertSame(0, $ticket->sold);
        $this->assertSame(0, $ticket->reserved);
    }

    public function test_eo_cannot_use_ticket_from_another_event(): void
    {
        $owner = $this->user('eo');
        $eventA = $this->event($owner);
        $eventB = $this->event($this->user('eo'));
        $ticketB = $this->ticket($eventB);

        $this->actingAs($owner)
            ->post(route('eo.events.tickets.store', $eventB), $this->ticketData())
            ->assertNotFound();

        $this->actingAs($owner)
            ->get(route('eo.events.tickets.edit', [$eventA, $ticketB]))
            ->assertNotFound();

        $this->actingAs($owner)
            ->put(route('eo.events.tickets.update', [$eventA, $ticketB]), $this->ticketData())
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('eo.events.tickets.destroy', [$eventA, $ticketB]))
            ->assertNotFound();

        $this->assertDatabaseHas('tickets', ['id' => $ticketB->id, 'event_id' => $eventB->id]);
    }

    public function test_ticket_cannot_be_moved_or_inventory_fields_spoofed_on_update(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $otherEvent = $this->event($this->user('eo'));
        $ticket = $this->ticket($event);

        $this->actingAs($owner)
            ->put(route('eo.events.tickets.update', [$event, $ticket]), [
                ...$this->ticketData(['name' => 'Updated VIP']),
                'event_id' => $otherEvent->id,
                'sold' => 99,
                'reserved' => 99,
            ])
            ->assertRedirect(route('eo.events.tickets.index', $event));

        $ticket->refresh();
        $this->assertSame($event->id, $ticket->event_id);
        $this->assertSame('Updated VIP', $ticket->name);
        $this->assertSame(0, $ticket->sold);
        $this->assertSame(0, $ticket->reserved);
    }

    public function test_quota_must_cover_sold_and_reserved_inventory(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, ['sold' => 4, 'reserved' => 3]);

        $this->actingAs($owner)
            ->put(route('eo.events.tickets.update', [$event, $ticket]), $this->ticketData(['quota' => 6]))
            ->assertSessionHasErrors('quota');

        $this->assertSame(100, $ticket->fresh()->quota);
    }

    public function test_pending_event_locks_ticket_mutations(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, ['workflow_status' => 'submitted', 'approval_status' => 'pending']);
        $ticket = $this->ticket($event);
        $lockedMessage = 'Ticket sedang dikunci karena event sedang dalam proses review.';

        $this->actingAs($owner)
            ->get(route('eo.events.tickets.create', $event))
            ->assertRedirect(route('eo.events.tickets.index', $event))
            ->assertSessionHas('error', $lockedMessage);
        $this->post(route('eo.events.tickets.store', $event), $this->ticketData())
            ->assertSessionHas('error', $lockedMessage);
        $this->get(route('eo.events.tickets.edit', [$event, $ticket]))
            ->assertSessionHas('error', $lockedMessage);
        $this->put(route('eo.events.tickets.update', [$event, $ticket]), $this->ticketData())
            ->assertSessionHas('error', $lockedMessage);
        $this->delete(route('eo.events.tickets.destroy', [$event, $ticket]))
            ->assertSessionHas('error', $lockedMessage);

        $this->assertDatabaseCount('tickets', 1);
        $this->assertSame('VIP', $ticket->fresh()->name);
    }

    public function test_rejected_event_allows_ticket_edits_without_changing_rejection_state(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, [
            'workflow_status' => 'submitted',
            'approval_status' => 'rejected',
            'rejection_reason' => 'Perbaiki informasi ticket.',
        ]);
        $ticket = $this->ticket($event);

        $this->actingAs($owner)
            ->put(route('eo.events.tickets.update', [$event, $ticket]), $this->ticketData(['name' => 'Regular']))
            ->assertRedirect(route('eo.events.tickets.index', $event));

        $this->assertSame('Regular', $ticket->fresh()->name);
        $this->assertSame('rejected', $event->fresh()->approval_status);
        $this->assertSame('submitted', $event->fresh()->workflow_status);
    }

    public function test_approved_event_locks_name_and_price_but_allows_safe_quota_change(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, ['workflow_status' => 'submitted', 'approval_status' => 'approved']);
        $ticket = $this->ticket($event, ['sold' => 5, 'reserved' => 3]);

        $this->actingAs($owner)
            ->put(route('eo.events.tickets.update', [$event, $ticket]), $this->ticketData(['name' => 'New Name']))
            ->assertSessionHas('error', 'Nama dan harga ticket tidak dapat diubah setelah event disetujui.');

        $this->put(route('eo.events.tickets.update', [$event, $ticket]), $this->ticketData(['price' => 300000]))
            ->assertSessionHas('error', 'Nama dan harga ticket tidak dapat diubah setelah event disetujui.');

        $this->put(route('eo.events.tickets.update', [$event, $ticket]), $this->ticketData([
            'name' => 'VIP',
            'price' => 250000,
            'quota' => 120,
        ]))->assertRedirect(route('eo.events.tickets.index', $event));

        $this->assertSame(120, $ticket->fresh()->quota);
        $this->assertSame(5, $ticket->fresh()->sold);
        $this->assertSame(3, $ticket->fresh()->reserved);
    }

    public function test_ticket_with_order_cannot_be_deleted(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event);
        Order::create([
            'order_code' => 'ORD-' . uniqid(),
            'user_id' => $this->user('user')->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'total_price' => $ticket->price,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->actingAs($owner)
            ->delete(route('eo.events.tickets.destroy', [$event, $ticket]))
            ->assertSessionHas('error', 'Ticket tidak dapat dihapus karena sudah memiliki transaksi.');

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    public function test_event_submit_without_ticket_is_rejected(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);

        $this->actingAs($owner)
            ->post(route('eo.events.submit', $event))
            ->assertRedirect(route('eo.events.tickets.index', $event))
            ->assertSessionHas('error', 'Tambahkan setidaknya satu tipe tiket sebelum mengajukan event.');

        $this->assertSame('draft', $event->fresh()->workflow_status);
        $this->assertSame('pending', $event->fresh()->approval_status);
    }

    public function test_event_submit_succeeds_with_at_least_one_ticket(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $this->ticket($event);

        $this->actingAs($owner)
            ->post(route('eo.events.submit', $event))
            ->assertRedirect(route('eo.events.index'))
            ->assertSessionHas('success', 'Event berhasil dikirim untuk ditinjau Admin.');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'workflow_status' => 'submitted',
            'approval_status' => 'pending',
            'rejection_reason' => null,
        ]);
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Ticket ' . ucfirst($role),
            'email' => $role . '-' . uniqid() . '@ticket.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function event(User $owner, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'user_id' => $owner->id,
            'name' => 'Ticket Test Event',
            'category' => 'Music',
            'location' => 'Jakarta',
            'venue' => 'Test Venue',
            'event_date' => today()->addDays(10)->toDateString(),
            'seating_type' => 'general_admission',
            'status' => 'coming_soon',
            'workflow_status' => 'draft',
            'approval_status' => 'pending',
        ], $overrides));
    }

    private function ticket(Event $event, array $overrides = []): Ticket
    {
        return $event->tickets()->create(array_merge([
            'name' => 'VIP',
            'price' => 250000,
            'quota' => 100,
            'sold' => 0,
            'reserved' => 0,
            'description' => 'Premium access.',
        ], $overrides));
    }

    private function ticketData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'VIP',
            'price' => 250000,
            'quota' => 100,
            'description' => 'Premium access.',
        ], $overrides);
    }
}
