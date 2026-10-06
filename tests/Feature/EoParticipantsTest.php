<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EoParticipantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_eo_can_view_issued_participants_and_numbered_seat(): void
    {
        $owner = $this->user('eo', 'Event Owner');
        $participant = $this->user('user', 'Nina Participant');
        $event = $this->event($owner, 'Owned Festival');
        $ticket = $this->ticket($event, 'VIP');
        $seat = Seat::create([
            'event_id' => $event->id,
            'seat_code' => 'A-01',
            'section' => 'A',
            'row' => '1',
            'status' => 'sold',
        ]);
        $this->issueTicket($ticket, $participant, 'TIX-OWNED-001', 'issued', $seat);

        $this->actingAs($owner)
            ->get(route('eo.participants.index'))
            ->assertOk()
            ->assertSee('Nina Participant')
            ->assertSee($participant->email)
            ->assertSee('Owned Festival')
            ->assertSee('VIP')
            ->assertSee('TIX-OWNED-001')
            ->assertSee('A-01')
            ->assertSee('Diterbitkan')
            ->assertSee('Total Peserta')
            ->assertSee('Total Tiket Diterbitkan');
    }

    public function test_normal_user_cannot_access_participants_page(): void
    {
        $this->actingAs($this->user('user', 'Regular User'))
            ->get(route('eo.participants.index'))
            ->assertForbidden();
    }

    public function test_eo_only_sees_participants_from_owned_events_and_cannot_filter_foreign_event(): void
    {
        $owner = $this->user('eo', 'Event Owner');
        $otherOwner = $this->user('eo', 'Other Owner');
        $ownedEvent = $this->event($owner, 'Owned Festival');
        $foreignEvent = $this->event($otherOwner, 'Private Festival');
        $ownedTicket = $this->ticket($ownedEvent, 'General Admission');
        $foreignTicket = $this->ticket($foreignEvent, 'General Admission');
        $this->issueTicket($ownedTicket, $this->user('user', 'Owned Participant'), 'TIX-OWNED-002');
        $this->issueTicket($foreignTicket, $this->user('user', 'Private Participant'), 'TIX-PRIVATE-001');

        $this->actingAs($owner)
            ->get(route('eo.participants.index'))
            ->assertOk()
            ->assertSee('TIX-OWNED-002')
            ->assertDontSee('TIX-PRIVATE-001')
            ->assertDontSee('Private Participant');

        $this->actingAs($owner)
            ->from(route('eo.participants.index'))
            ->get(route('eo.participants.index', ['event_id' => $foreignEvent->id]))
            ->assertRedirect(route('eo.participants.index'))
            ->assertSessionHasErrors('event_id');
    }

    public function test_event_status_and_search_filters_only_return_matching_ticket_instances(): void
    {
        $owner = $this->user('eo', 'Event Owner');
        $holder = $this->user('user', 'Nina Filtered');
        $event = $this->event($owner, 'Main Festival');
        $otherEvent = $this->event($owner, 'Other Festival');
        $eventTicket = $this->ticket($event, 'General Admission');
        $otherTicket = $this->ticket($otherEvent, 'General Admission');
        $this->issueTicket($eventTicket, $holder, 'TIX-MATCH-001', 'issued');
        $this->issueTicket($eventTicket, $holder, 'TIX-USED-001', 'used');
        $this->issueTicket($otherTicket, $holder, 'TIX-OTHER-001', 'issued');

        $this->actingAs($owner)
            ->get(route('eo.participants.index', [
                'event_id' => $event->id,
                'status' => 'issued',
                'search' => 'TIX-MATCH-001',
            ]))
            ->assertOk()
            ->assertSee('TIX-MATCH-001')
            ->assertDontSee('TIX-USED-001')
            ->assertDontSee('TIX-OTHER-001');

        $this->actingAs($owner)
            ->get(route('eo.participants.index', ['search' => 'Nina Filtered']))
            ->assertOk()
            ->assertSee('TIX-MATCH-001')
            ->assertSee('TIX-USED-001');

        $this->actingAs($owner)
            ->get(route('eo.participants.index', ['search' => $holder->email]))
            ->assertOk()
            ->assertSee('TIX-MATCH-001');
    }

    public function test_empty_state_ignores_pending_orders_without_issued_ticket_instances(): void
    {
        $owner = $this->user('eo', 'Event Owner');
        $buyer = $this->user('user', 'Pending Buyer');
        $event = $this->event($owner, 'Future Festival');
        $ticket = $this->ticket($event, 'General Admission');
        Order::create([
            'order_code' => 'ORD-PENDING-001',
            'user_id' => $buyer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'total_price' => 50000,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('eo.participants.index'))
            ->assertOk()
            ->assertSee('Belum ada peserta')
            ->assertSee('Peserta akan muncul di sini setelah tiket diterbitkan.')
            ->assertDontSee('Pending Buyer')
            ->assertDontSee('ORD-PENDING-001');
    }

    public function test_general_admission_participant_has_no_fabricated_seat(): void
    {
        $owner = $this->user('eo', 'Event Owner');
        $event = $this->event($owner, 'General Admission Festival');
        $ticket = $this->ticket($event, 'General Admission');
        $this->issueTicket($ticket, $this->user('user', 'GA Participant'), 'TIX-GA-001');

        $this->actingAs($owner)
            ->get(route('eo.participants.index'))
            ->assertOk()
            ->assertSee('TIX-GA-001')
            ->assertSee('<td>-</td>', false);
    }

    public function test_filters_are_preserved_across_participant_pagination(): void
    {
        $owner = $this->user('eo', 'Event Owner');
        $participant = $this->user('user', 'Page Participant');
        $event = $this->event($owner, 'Page Festival');
        $ticket = $this->ticket($event, 'General Admission');

        for ($index = 1; $index <= 16; $index++) {
            $this->issueTicket($ticket, $participant, sprintf('TIX-PAGE-%02d', $index));
        }

        $this->actingAs($owner)
            ->get(route('eo.participants.index', [
                'event_id' => $event->id,
                'status' => 'issued',
                'search' => 'Page',
                'page' => 2,
            ]))
            ->assertOk()
            ->assertSee('event_id=' . $event->id)
            ->assertSee('status=issued')
            ->assertSee('search=Page');
    }

    private function user(string $role, string $name): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '-' . uniqid() . '@example.test',
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
            'description' => 'Participant test event.',
        ]);
    }

    private function ticket(Event $event, string $name): Ticket
    {
        return $event->tickets()->create([
            'name' => $name,
            'price' => 50000,
            'quota' => 100,
            'sold' => 0,
            'reserved' => 0,
        ]);
    }

    private function issueTicket(
        Ticket $ticket,
        User $participant,
        string $code,
        string $status = 'issued',
        ?Seat $seat = null
    ): TicketInstance {
        $order = Order::create([
            'order_code' => 'ORD-' . $code,
            'user_id' => $participant->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'total_price' => $ticket->price,
            'status' => 'paid',
        ]);

        return TicketInstance::create([
            'order_id' => $order->id,
            'ticket_id' => $ticket->id,
            'user_id' => $participant->id,
            'seat_id' => $seat?->id,
            'ticket_code' => $code,
            'qr_token' => 'QR-' . $code,
            'status' => $status,
        ]);
    }
}