<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventSection;
use App\Models\Order;
use App\Models\OrderSeat;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EoSeatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_buyer_cannot_access_seating(): void
    {
        $event = $this->event($this->user('eo'));
        $this->get(route('eo.events.seating.index', $event))->assertRedirect(route('login'));
        $this->actingAs($this->user('user'))
            ->get(route('eo.events.seating.index', $event))
            ->assertForbidden();
    }

    public function test_eo_can_create_section_for_owned_event_and_ticket(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');

        $this->actingAs($owner)
            ->post(route('eo.events.sections.store', $event), [
                'code' => ' vip ',
                'name' => 'VIP Floor',
                'ticket_id' => $ticket->id,
            ])
            ->assertRedirect(route('eo.events.seating.index', $event));

        $section = EventSection::query()->firstOrFail();
        $this->assertSame('VIP', $section->code);
        $this->assertSame($event->id, $section->event_id);
        $this->assertSame($ticket->id, $section->ticket_id);
        $this->assertSame(0, $ticket->fresh()->quota);
    }

    public function test_section_cannot_use_ticket_from_another_event(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $otherTicket = $this->ticket($this->event($this->user('eo')), 'Other');

        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->post(route('eo.events.sections.store', $event), [
                'code' => 'VIP',
                'name' => 'VIP',
                'ticket_id' => $otherTicket->id,
            ])
            ->assertSessionHasErrors('ticket_id');

        $this->assertDatabaseCount('event_sections', 0);
    }

    public function test_section_code_is_unique_within_event_and_normalized(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $this->section($event, $ticket, 'VIP');

        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->post(route('eo.events.sections.store', $event), [
                'code' => 'vip',
                'name' => 'VIP 2',
                'ticket_id' => $ticket->id,
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_eo_cannot_access_another_event_or_section(): void
    {
        $owner = $this->user('eo');
        $eventA = $this->event($owner);
        $eventB = $this->event($this->user('eo'));
        $ticketB = $this->ticket($eventB, 'B');
        $sectionB = $this->section($eventB, $ticketB, 'B');

        $this->actingAs($owner)->get(route('eo.events.seating.index', $eventB))->assertNotFound();
        $this->actingAs($owner)
            ->put(route('eo.events.sections.update', [$eventA, $sectionB]), [
                'code' => 'B', 'name' => 'B', 'ticket_id' => $ticketB->id,
            ])
            ->assertNotFound();
    }

    public function test_duplicate_code_can_exist_in_different_events(): void
    {
        $eventA = $this->event($this->user('eo'));
        $eventB = $this->event($this->user('eo'));
        $sectionA = $this->section($eventA, $this->ticket($eventA, 'A'), 'VIP');
        $sectionB = $this->section($eventB, $this->ticket($eventB, 'B'), 'VIP');

        $this->assertNotSame($sectionA->event_id, $sectionB->event_id);
    }

    public function test_pending_event_locks_section_and_seat_topology(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $section = $this->section($event, $ticket, 'VIP');
        $event->update(['workflow_status' => 'submitted', 'approval_status' => 'pending']);

        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->post(route('eo.events.sections.store', $event), [
                'code' => 'REG', 'name' => 'Regular', 'ticket_id' => $ticket->id,
            ])
            ->assertSessionHasErrors('seating');
        $this->post(route('eo.events.sections.seats.generate', [$event, $section]), [
            'rows' => 'A', 'seats_per_row' => 2,
        ])->assertSessionHasErrors('seating');
        $this->assertDatabaseCount('seats', 0);
    }

    public function test_rejected_event_allows_section_edit(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, ['workflow_status' => 'submitted', 'approval_status' => 'rejected']);
        $ticket = $this->ticket($event, 'VIP');
        $section = $this->section($event, $ticket, 'VIP');

        $this->actingAs($owner)
            ->put(route('eo.events.sections.update', [$event, $section]), [
                'code' => 'VVIP', 'name' => 'VVIP Floor', 'ticket_id' => $ticket->id,
            ])
            ->assertRedirect(route('eo.events.seating.index', $event));

        $this->assertSame('rejected', $event->fresh()->approval_status);
        $this->assertSame('VVIP', $section->fresh()->code);
    }

    public function test_section_with_seats_cannot_be_moved_to_another_ticket(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $otherTicket = $this->ticket($event, 'Regular');
        $section = $this->section($event, $ticket, 'VIP');
        $this->generate($owner, $event, $section, 1);

        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->put(route('eo.events.sections.update', [$event, $section]), [
                'code' => 'VIP', 'name' => 'VIP', 'ticket_id' => $otherTicket->id,
            ])
            ->assertSessionHasErrors('ticket_id', 'Section yang sudah memiliki kursi tidak dapat dipindahkan ke tipe tiket lain.');
    }

    public function test_generated_seats_use_section_prefix_and_sync_ticket_quota(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $vip = $this->ticket($event, 'VIP');
        $reg = $this->ticket($event, 'Regular');
        $vipSection = $this->section($event, $vip, 'VIP');
        $regSection = $this->section($event, $reg, 'REG');

        $this->generate($owner, $event, $vipSection, 2, 'A,B');
        $this->generate($owner, $event, $regSection, 2, 'A');

        $this->assertSame(4, $vip->fresh()->quota);
        $this->assertSame(2, $reg->fresh()->quota);
        $this->assertDatabaseHas('seats', [
            'event_id' => $event->id, 'section_id' => $vipSection->id,
            'section' => 'VIP Section', 'row' => 'A', 'seat_code' => 'VIP-A01', 'status' => 'available',
        ]);
        $this->assertDatabaseHas('seats', ['event_id' => $event->id, 'seat_code' => 'VIP-B02']);
        $this->assertDatabaseHas('seats', ['event_id' => $event->id, 'seat_code' => 'REG-A01']);
        $this->assertSame(6, Seat::where('event_id', $event->id)->count());
    }

    public function test_eo_cannot_generate_more_than_ten_thousand_seats(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $section = $this->section($event, $this->ticket($event, 'VIP'), 'VIP');

        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->post(route('eo.events.sections.seats.generate', [$event, $section]), [
                'rows' => 'A,B', 'seats_per_row' => 5001,
            ])
            ->assertSessionHasErrors('seats_per_row');

        $this->assertSame(0, Seat::count());
    }

    public function test_available_seat_can_be_deleted_and_quota_is_resynchronized(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $section = $this->section($event, $ticket, 'VIP');
        $this->generate($owner, $event, $section, 2);
        $seat = $section->seats()->where('seat_code', 'VIP-A01')->firstOrFail();

        $this->actingAs($owner)
            ->delete(route('eo.events.sections.seats.destroy', [$event, $section, $seat]))
            ->assertRedirect(route('eo.events.seating.index', $event));

        $this->assertDatabaseMissing('seats', ['id' => $seat->id]);
        $this->assertSame(1, $ticket->fresh()->quota);
    }

    public function test_reserved_or_sold_seat_cannot_be_deleted_or_lower_quota_below_sold(): void
    {
        foreach (['reserved', 'sold'] as $status) {
            $owner = $this->user('eo');
            $event = $this->event($owner);
            $ticket = $this->ticket($event, 'VIP');
            $section = $this->section($event, $ticket, 'VIP');
            $this->generate($owner, $event, $section, 1);
            $seat = $section->seats()->firstOrFail();
            $seat->update(['status' => $status]);
            if ($status === 'sold') {
                $ticket->update(['sold' => 1]);
            }

            $this->actingAs($owner)
                ->from(route('eo.events.seating.index', $event))
                ->delete(route('eo.events.sections.seats.destroy', [$event, $section, $seat]))
                ->assertSessionHasErrors('seat');

            if ($status === 'sold') {
                $ticket->update(['sold' => 2]);
                $this->generate($owner, $event, $section, 1, 'B');
                $availableSeat = $section->seats()->where('row', 'B')->firstOrFail();
                $this->actingAs($owner)
                    ->from(route('eo.events.seating.index', $event))
                    ->delete(route('eo.events.sections.seats.destroy', [$event, $section, $availableSeat]))
                    ->assertSessionHasErrors('quota');
                $this->assertSame(2, $ticket->fresh()->quota);
            }
        }
    }

    public function test_section_delete_removes_available_seats_and_refuses_transaction_seats(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $section = $this->section($event, $ticket, 'VIP');
        $this->generate($owner, $event, $section, 2);

        $this->actingAs($owner)
            ->delete(route('eo.events.sections.destroy', [$event, $section]))
            ->assertRedirect(route('eo.events.seating.index', $event));
        $this->assertSame(0, $ticket->fresh()->quota);
        $this->assertDatabaseCount('seats', 0);
        $this->assertDatabaseCount('event_sections', 0);

        $section = $this->section($event, $ticket, 'VIP');
        $seat = $this->seat($event, $section, ['status' => 'reserved']);
        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->delete(route('eo.events.sections.destroy', [$event, $section]))
            ->assertSessionHasErrors('section');
        $this->assertDatabaseHas('seats', ['id' => $seat->id]);
    }

    public function test_section_with_transaction_history_cannot_be_deleted(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $section = $this->section($event, $ticket, 'VIP');
        $this->generate($owner, $event, $section, 1);
        $seat = $section->seats()->firstOrFail();
        $order = Order::create([
            'order_code' => 'ORD-' . uniqid(),
            'user_id' => $this->user('user')->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'total_price' => $ticket->price,
            'status' => 'cancelled',
            'expires_at' => now()->subMinute(),
        ]);
        OrderSeat::create(['order_id' => $order->id, 'seat_id' => $seat->id, 'status' => 'cancelled']);

        $this->actingAs($owner)
            ->from(route('eo.events.seating.index', $event))
            ->delete(route('eo.events.sections.destroy', [$event, $section]))
            ->assertSessionHasErrors('section');

        $this->assertDatabaseHas('event_sections', ['id' => $section->id]);
    }

    public function test_approved_topology_is_read_only_after_transaction_or_seat_is_active(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, ['workflow_status' => 'submitted', 'approval_status' => 'approved']);
        $ticket = $this->ticket($event, 'VIP');
        $section = $this->section($event, $ticket, 'VIP');
        $seat = $this->seat($event, $section, ['status' => 'reserved']);

        $this->actingAs($owner)
            ->get(route('eo.events.seating.index', $event))
            ->assertOk()
            ->assertSee('Konfigurasi seating read-only');
        $this->from(route('eo.events.seating.index', $event))
            ->post(route('eo.events.sections.seats.generate', [$event, $section]), [
                'rows' => 'B', 'seats_per_row' => 1,
            ])
            ->assertSessionHasErrors('seating');
        $this->assertSame('reserved', $seat->fresh()->status);
    }

    public function test_admin_seat_create_remains_compatible_with_legacy_columns(): void
    {
        $admin = $this->user('admin');
        $event = $this->event($admin);

        $this->actingAs($admin)
            ->post(route('admin.seats.store'), [
                'event_id' => $event->id,
                'seat_code' => 'OLD-A01',
                'section' => 'Old Section',
                'row' => 'A',
                'status' => 'available',
                'section_id' => 9999,
            ])
            ->assertRedirect(route('admin.seats.index', ['event_id' => $event->id]));

        $this->assertDatabaseHas('seats', [
            'event_id' => $event->id,
            'section_id' => null,
            'seat_code' => 'OLD-A01',
            'section' => 'Old Section',
            'row' => 'A',
            'status' => 'available',
        ]);
    }

    public function test_numbered_ticket_quota_is_system_controlled_in_ticket_crud(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);

        $this->actingAs($owner)
            ->post(route('eo.events.tickets.store', $event), [
                'name' => 'VIP', 'price' => 500000, 'quota' => 999, 'sold' => 30, 'reserved' => 40,
            ])
            ->assertRedirect(route('eo.events.tickets.index', $event));
        $ticket = Ticket::where('event_id', $event->id)->firstOrFail();
        $this->assertSame(0, $ticket->quota);
        $this->assertSame(0, $ticket->sold);
        $this->assertSame(0, $ticket->reserved);

        $this->put(route('eo.events.tickets.update', [$event, $ticket]), [
            'name' => 'VIP', 'price' => 500000, 'quota' => 5000, 'sold' => 50, 'reserved' => 50,
        ])->assertRedirect(route('eo.events.tickets.index', $event));
        $this->assertSame(0, $ticket->fresh()->quota);
    }

    public function test_ticket_type_page_shows_read_only_numbered_quota_and_seating_page(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');

        $this->actingAs($owner)
            ->get(route('eo.events.tickets.index', $event))
            ->assertOk()
            ->assertSee('Kuota kursi untuk event bernomor dihitung dari kursi yang dikonfigurasi.')
            ->assertSee(route('eo.events.seating.index', $event));
        $this->get(route('eo.events.tickets.create', $event))
            ->assertOk()
            ->assertDontSee('name="quota"', false)
            ->assertSee('Dihitung dari konfigurasi seat');
        $this->get(route('eo.events.tickets.edit', [$event, $ticket]))
            ->assertOk()
            ->assertDontSee('name="quota"', false);
        $this->get(route('eo.events.seating.index', $event))
            ->assertOk()
            ->assertSee('Seating Configuration')
            ->assertSee('Tambah Section');
    }

    public function test_existing_legacy_seat_remains_unmapped_and_visible_to_admin_style_flow(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        $ticket = $this->ticket($event, 'VIP');
        $legacySeat = Seat::create([
            'event_id' => $event->id, 'seat_code' => 'A01', 'section' => 'OLD', 'row' => 'A', 'status' => 'available',
        ]);

        $this->assertNull($legacySeat->fresh()->section_id);
        $this->assertSame(100, $ticket->fresh()->quota);
        $this->assertDatabaseHas('seats', ['id' => $legacySeat->id, 'section' => 'OLD', 'seat_code' => 'A01']);
    }

    private function generate(User $owner, Event $event, EventSection $section, int $count, string $rows = 'A'): void
    {
        $this->actingAs($owner)
            ->post(route('eo.events.sections.seats.generate', [$event, $section]), [
                'rows' => $rows,
                'seats_per_row' => $count,
            ])
            ->assertRedirect(route('eo.events.seating.index', $event));
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Seat ' . ucfirst($role),
            'email' => $role . '-' . uniqid() . '@seating.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function event(User $owner, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'user_id' => $owner->id,
            'name' => 'Numbered Event',
            'category' => 'Music',
            'location' => 'Jakarta',
            'venue' => 'Hall A',
            'event_date' => today()->addDays(10)->toDateString(),
            'seating_type' => 'numbered_seat',
            'status' => 'coming_soon',
            'workflow_status' => 'draft',
            'approval_status' => 'pending',
        ], $overrides));
    }

    private function ticket(Event $event, string $name): Ticket
    {
        return $event->tickets()->create([
            'name' => $name,
            'price' => $name === 'Regular' ? 250000 : 500000,
            'quota' => 100,
            'sold' => 0,
            'reserved' => 0,
        ]);
    }

    private function section(Event $event, Ticket $ticket, string $code): EventSection
    {
        return $event->sections()->create([
            'ticket_id' => $ticket->id,
            'code' => $code,
            'name' => $code . ' Section',
        ]);
    }

    private function seat(Event $event, EventSection $section, array $overrides = []): Seat
    {
        return Seat::create(array_merge([
            'event_id' => $event->id,
            'section_id' => $section->id,
            'seat_code' => $section->code . '-A01',
            'section' => $section->name,
            'row' => 'A',
            'status' => 'available',
        ], $overrides));
    }
}
