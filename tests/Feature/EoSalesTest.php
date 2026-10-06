<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EoSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_eo_can_view_real_sales_for_owned_events_only(): void
    {
        $owner = $this->user('eo');
        $ownedOrder = $this->order($this->event($owner), 'OWNED-ORDER', 'paid', 'pending');
        $otherOrder = $this->order($this->event($this->user('eo'), 'Other EO Event'), 'OTHER-ORDER');

        $this->actingAs($owner)
            ->get(route('eo.sales.index'))
            ->assertOk()
            ->assertSee('OWNED-ORDER')
            ->assertSee('Sales Event')
            ->assertSee('General Admission')
            ->assertSee('Pending')
            ->assertDontSee($otherOrder->order_code);

        $this->assertDatabaseHas('orders', ['id' => $ownedOrder->id]);
    }

    public function test_eo_sees_compact_empty_state_when_there_are_no_sales(): void
    {
        $this->actingAs($this->user('eo'))
            ->get(route('eo.sales.index'))
            ->assertOk()
            ->assertSee('Belum ada transaksi.')
            ->assertSee('Transaksi untuk event yang kamu miliki akan muncul di sini.');
    }

    public function test_non_eo_cannot_access_sales_page(): void
    {
        $this->actingAs($this->user('user'))
            ->get(route('eo.sales.index'))
            ->assertForbidden();
    }

    public function test_sales_filters_are_applied_within_owned_events(): void
    {
        $owner = $this->user('eo');
        $selectedEvent = $this->event($owner, 'Selected Event');
        $otherOwnedEvent = $this->event($owner, 'Other Owned Event');
        $this->order($selectedEvent, 'FILTER-MATCH', 'paid', 'paid');
        $this->order($selectedEvent, 'FILTER-STATUS-MISMATCH', 'pending', 'pending');
        $this->order($otherOwnedEvent, 'FILTER-EVENT-MISMATCH', 'paid', 'paid');

        $this->actingAs($owner)
            ->get(route('eo.sales.index', [
                'event_id' => $selectedEvent->id,
                'status' => 'paid',
                'payment_status' => 'paid',
            ]))
            ->assertOk()
            ->assertSee('FILTER-MATCH')
            ->assertSee('name="event_id"', false)
            ->assertSee('name="status"', false)
            ->assertSee('name="payment_status"', false)
            ->assertSee('Terapkan Filter')
            ->assertDontSee('FILTER-STATUS-MISMATCH')
            ->assertDontSee('FILTER-EVENT-MISMATCH');
    }

    public function test_foreign_event_filter_is_rejected_without_exposing_sales(): void
    {
        $owner = $this->user('eo');
        $foreignEvent = $this->event($this->user('eo'), 'Private Event');
        $this->order($foreignEvent, 'PRIVATE-ORDER');

        $this->actingAs($owner)
            ->from(route('eo.sales.index'))
            ->get(route('eo.sales.index', ['event_id' => $foreignEvent->id]))
            ->assertRedirect(route('eo.sales.index'))
            ->assertSessionHasErrors('event_id');
    }

    public function test_filtered_pagination_keeps_filters_and_never_includes_another_owners_orders(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);
        for ($index = 1; $index <= 16; $index++) {
            $this->order($event, 'OWN-PAGE-' . $index, 'paid', 'paid');
        }
        $otherEvent = $this->event($this->user('eo'), 'Other EO Event');
        $this->order($otherEvent, 'FOREIGN-PAGE-ORDER', 'paid', 'paid');

        $this->actingAs($owner)
            ->get(route('eo.sales.index', [
                'event_id' => $event->id,
                'status' => 'paid',
                'payment_status' => 'paid',
                'page' => 2,
            ]))
            ->assertOk()
            ->assertSee('OWN-PAGE-1')
            ->assertSee('event_id=' . $event->id)
            ->assertSee('status=paid')
            ->assertDontSee('FOREIGN-PAGE-ORDER');
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Sales ' . ucfirst($role),
            'email' => $role . '-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function event(User $owner, string $name = 'Sales Event'): Event
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
            'description' => 'Sales test event.',
        ]);
    }

    private function order(Event $event, string $code, string $orderStatus = 'pending', ?string $paymentStatus = null): Order
    {
        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => 'General Admission',
            'price' => 50000,
            'quota' => 100,
            'sold' => 0,
            'description' => null,
        ]);

        $order = Order::create([
            'order_code' => $code,
            'user_id' => $event->user_id,
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'total_price' => 100000,
            'status' => $orderStatus,
        ]);

        if ($paymentStatus !== null) {
            Payment::create([
                'order_id' => $order->id,
                'payment_code' => 'PAY-' . $code,
                'amount' => 100000,
                'status' => $paymentStatus,
            ]);
        }

        return $order;
    }
}