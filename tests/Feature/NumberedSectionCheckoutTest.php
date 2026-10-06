<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventSection;
use App\Models\Order;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NumberedSectionCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_cannot_checkout_seat_from_another_ticket_type_section(): void
    {
        $this->mock(MidtransService::class, fn ($mock) => $mock->shouldNotReceive('createSnapToken'));
        [$user, $event, $vip, $regular, $vipSection, $regularSection] = $this->fixture();
        $seat = $this->seat($event, $regularSection, 'REG-A01');
        $this->assertSame($regularSection->id, $seat->fresh()->section_id);

        $this->actingAs($user)->postJson(route('checkout.store'), [
            'ticket_id' => $vip->id,
            'quantity' => 1,
            'seat_ids' => [$seat->id],
            'payment_method' => 'qris',
        ])->assertUnprocessable()->assertJsonValidationErrors('seat_ids');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_cannot_mix_event_or_ticket_types(): void
    {
        $this->mock(MidtransService::class, fn ($mock) => $mock->shouldNotReceive('createSnapToken'));
        [$user, $event, $vip, $regular, $vipSection, $regularSection] = $this->fixture();
        $otherEvent = $this->event($this->user('eo'));
        $otherTicket = $this->ticket($otherEvent, 'OTHER', 1);
        $otherSection = $this->section($otherEvent, $otherTicket, 'OTHER');
        $foreignSeat = $this->seat($otherEvent, $otherSection, 'OTHER-A01');

        $this->actingAs($user)->postJson(route('checkout.store'), [
            'ticket_id' => $vip->id, 'quantity' => 1, 'seat_ids' => [$foreignSeat->id], 'payment_method' => 'qris',
        ])->assertUnprocessable()->assertJsonValidationErrors('seat_ids');

        $vipSeat = $this->seat($event, $vipSection, 'VIP-A01');
        $regularSeat = $this->seat($event, $regularSection, 'REG-A01');

        $this->postJson(route('checkout.store'), [
            'ticket_id' => $vip->id,
            'quantity' => 2,
            'seat_ids' => [$vipSeat->id, $regularSeat->id],
            'payment_method' => 'qris',
        ])->assertUnprocessable()->assertJsonValidationErrors('seat_ids');
    }

    public function test_numbered_checkout_requires_available_seats_and_exact_quantity(): void
    {
        $this->mock(MidtransService::class, fn ($mock) => $mock->shouldNotReceive('createSnapToken'));
        [$user, $event, $ticket, , $section] = $this->fixture();
        $seat = $this->seat($event, $section, 'VIP-A01');

        $this->actingAs($user)->postJson(route('checkout.store'), [
            'ticket_id' => $ticket->id, 'quantity' => 2, 'seat_ids' => [$seat->id], 'payment_method' => 'qris',
        ])->assertUnprocessable()->assertJsonValidationErrors('seat_ids');

        $ticket->update(['quota' => 1]);
        $seat->update(['status' => 'reserved']);
        $this->postJson(route('checkout.store'), [
            'ticket_id' => $ticket->id, 'quantity' => 1, 'seat_ids' => [$seat->id], 'payment_method' => 'qris',
        ])->assertUnprocessable()->assertJsonValidationErrors('seat_ids');
    }

    public function test_mapped_seat_checkout_and_issuance_preserve_ticket_and_seat_links(): void
    {
        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')->once()->andReturn('section-snap-token');
            $mock->shouldReceive('getClientKey')->once()->andReturn('test-client-key');
        });

        [$user, $event, $ticket, , $section] = $this->fixture();
        $ticket->update(['quota' => 2]);
        $seatA = $this->seat($event, $section, 'VIP-A01');
        $seatB = $this->seat($event, $section, 'VIP-A02');

        $response = $this->actingAs($user)->postJson(route('checkout.store'), [
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'seat_ids' => [$seatA->id, $seatB->id],
            'payment_method' => 'qris',
        ])->assertCreated()->assertJsonPath('order.total_price', '1000000.00');

        $order = Order::where('order_code', $response->json('order.order_code'))->firstOrFail();
        $this->assertSame('reserved', $seatA->fresh()->status);

        $this->postJson(route('checkout.simulate-payment', $order), [])
            ->assertOk()
            ->assertJsonPath('order.status', 'paid');

        $this->assertSame('sold', $seatA->fresh()->status);
        $this->assertSame('sold', $seatB->fresh()->status);
        $instances = TicketInstance::where('order_id', $order->id)->get();
        $this->assertCount(2, $instances);
        foreach ($instances as $instance) {
            $this->assertSame($ticket->id, $instance->ticket_id);
            $this->assertContains($instance->seat_id, [$seatA->id, $seatB->id]);
        }
    }

    public function test_public_numbered_seat_view_scopes_availability_and_seats_to_ticket_sections(): void
    {
        [$user, $event, $vip, $regular, $vipSection, $regularSection] = $this->fixture();
        $vipSeat = $this->seat($event, $vipSection, 'VIP-A01');
        $regularSeat = $this->seat($event, $regularSection, 'REG-A01');

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('data-ticket-has-sections="1"', false)
            ->assertSee('data-ticket-remaining="1"', false)
            ->assertSee('data-seat-code="VIP-A01"', false)
            ->assertSee('data-ticket-id="' . $vip->id . '"', false)
            ->assertSee('data-seat-code="REG-A01"', false)
            ->assertSee('data-ticket-id="' . $regular->id . '"', false);
    }

    public function test_legacy_unmapped_seats_keep_existing_numbered_checkout_behavior(): void
    {
        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')->once()->andReturn('legacy-token');
            $mock->shouldReceive('getClientKey')->once()->andReturn('test-client-key');
        });

        $user = $this->user('user');
        $event = $this->event($this->user('eo'));
        $ticket = $this->ticket($event, 'LEGACY', 1);
        $legacySeat = Seat::create([
            'event_id' => $event->id, 'seat_code' => 'A01', 'section' => 'Old Section', 'row' => 'A', 'status' => 'available',
        ]);

        $this->actingAs($user)->postJson(route('checkout.store'), [
            'ticket_id' => $ticket->id, 'quantity' => 1, 'seat_ids' => [$legacySeat->id], 'payment_method' => 'qris',
        ])->assertCreated();

        $this->assertNull($legacySeat->fresh()->section_id);
        $this->assertSame('reserved', $legacySeat->fresh()->status);
    }

    private function fixture(): array
    {
        $user = $this->user('user');
        $event = $this->event($this->user('eo'));
        $vip = $this->ticket($event, 'VIP', 2);
        $regular = $this->ticket($event, 'REG', 2);

        return [$user, $event, $vip, $regular, $this->section($event, $vip, 'VIP'), $this->section($event, $regular, 'REG')];
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Checkout ' . ucfirst($role),
            'email' => $role . '-' . uniqid() . '@section-checkout.test',
            'password' => 'password',
            'role' => $role,
            'email_verified_at' => now(),
        ]);
    }

    private function event(User $owner): Event
    {
        return Event::create([
            'user_id' => $owner->id,
            'name' => 'Section Checkout Event',
            'category' => 'Music',
            'location' => 'Jakarta',
            'venue' => 'Hall B',
            'event_date' => today()->addDays(10)->toDateString(),
            'seating_type' => 'numbered_seat',
            'status' => 'coming_soon',
            'workflow_status' => 'submitted',
            'approval_status' => 'approved',
        ]);
    }

    private function ticket(Event $event, string $name, int $quota): Ticket
    {
        return $event->tickets()->create([
            'name' => $name, 'price' => $name === 'REG' ? 250000 : 500000,
            'quota' => $quota, 'sold' => 0, 'reserved' => 0,
        ]);
    }

    private function section(Event $event, Ticket $ticket, string $code): EventSection
    {
        return $event->sections()->create(['ticket_id' => $ticket->id, 'code' => $code, 'name' => $code]);
    }

    private function seat(Event $event, EventSection $section, string $code): Seat
    {
        return Seat::create([
            'event_id' => $event->id, 'section_id' => $section->id, 'seat_code' => $code,
            'section' => $section->name, 'row' => 'A', 'status' => 'available',
        ]);
    }
}
