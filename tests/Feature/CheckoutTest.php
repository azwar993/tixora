<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderSeat;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_numbered_seat_checkout_and_payment_issue_tickets(): void
    {
        /*
         * Mock Midtrans.
         *
         * Test tidak menghubungi gateway Sandbox sungguhan.
         */
        $this->mock(MidtransService::class, function ($mock) {
            $mock
                ->shouldReceive('createSnapToken')
                ->once()
                ->andReturn('test-snap-token');

            $mock
                ->shouldReceive('getClientKey')
                ->once()
                ->andReturn('test-client-key');
        });

        $user = User::create([
            'name' => 'Checkout Test User',
            'email' => 'checkout-test@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $event = Event::create([
            'user_id' => $user->id,
            'name' => 'Checkout Test Event',
            'category' => 'Music',
            'location' => 'Jakarta',
            'venue' => 'Test Venue',
            'seating_type' => 'numbered_seat',
            'event_date' => today()->addDays(7),
            'status' => 'coming_soon',
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'description' => 'Event untuk integration test.',
            'image' => null,
        ]);

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => 60000,
            'quota' => 100,
            'sold' => 0,
            'reserved' => 0,
            'description' => 'Test ticket.',
        ]);

        $seatA01 = Seat::create([
            'event_id' => $event->id,
            'seat_code' => 'A01',
            'section' => 'REGULAR',
            'row' => 'A',
            'status' => 'available',
        ]);

        $seatA02 = Seat::create([
            'event_id' => $event->id,
            'seat_code' => 'A02',
            'section' => 'REGULAR',
            'row' => 'A',
            'status' => 'available',
        ]);

        /*
         * CHECKOUT
         */
        $checkoutResponse = $this
            ->actingAs($user)
            ->postJson(route('checkout.store'), [
                'ticket_id' => $ticket->id,
                'quantity' => 2,
                'seat_ids' => [
                    $seatA01->id,
                    $seatA02->id,
                ],
                'payment_method' => 'qris',
            ]);

        $checkoutResponse
            ->assertCreated()
            ->assertJsonPath(
                'order.status',
                'pending'
            )
            ->assertJsonPath(
                'order.quantity',
                2
            )
            ->assertJsonPath(
                'order.total_price',
                '120000.00'
            )
            ->assertJsonPath(
                'midtrans.client_key',
                'test-client-key'
            )
            ->assertJsonPath(
                'midtrans.snap_token',
                'test-snap-token'
            );

        $order = Order::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->firstOrFail();

        /*
         * ORDER
         */
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
            'quantity' => 2,
        ]);

        $this->assertNotNull(
            $order->expires_at
        );

        /*
         * PAYMENT
         */
        $payment = Payment::query()
            ->where('order_id', $order->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'order_id' => $order->id,
            'status' => 'pending',
            'payment_method' => 'qris',
            'amount' => '120000.00',
        ]);

        /*
         * SEAT RESERVED
         */
        $this->assertDatabaseHas('seats', [
            'id' => $seatA01->id,
            'status' => 'reserved',
        ]);

        $this->assertDatabaseHas('seats', [
            'id' => $seatA02->id,
            'status' => 'reserved',
        ]);

        /*
         * ORDER SEAT RESERVED
         */
        $this->assertDatabaseHas('order_seats', [
            'order_id' => $order->id,
            'seat_id' => $seatA01->id,
            'status' => 'reserved',
        ]);

        $this->assertDatabaseHas('order_seats', [
            'order_id' => $order->id,
            'seat_id' => $seatA02->id,
            'status' => 'reserved',
        ]);

        $this->assertSame(
            'pending',
            $payment->status
        );

        /*
         * SIMULATE PAYMENT
         */
        $paymentResponse = $this
            ->actingAs($user)
            ->postJson(
                route(
                    'checkout.simulate-payment',
                    $order
                ),
                []
            );

        $paymentResponse
            ->assertOk()
            ->assertJsonPath(
                'order.status',
                'paid'
            );

        /*
         * ORDER PAID
         */
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);

        /*
         * PAYMENT PAID
         */
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        $paidPayment = Payment::findOrFail(
            $payment->id
        );

        $this->assertNotNull(
            $paidPayment->paid_at
        );

        $this->assertNotNull(
            $paidPayment->transaction_id
        );

        /*
         * SEAT SOLD
         */
        $this->assertDatabaseHas('seats', [
            'id' => $seatA01->id,
            'status' => 'sold',
        ]);

        $this->assertDatabaseHas('seats', [
            'id' => $seatA02->id,
            'status' => 'sold',
        ]);

        /*
         * ORDER SEAT SOLD
         */
        $this->assertDatabaseHas('order_seats', [
            'order_id' => $order->id,
            'seat_id' => $seatA01->id,
            'status' => 'sold',
        ]);

        $this->assertDatabaseHas('order_seats', [
            'order_id' => $order->id,
            'seat_id' => $seatA02->id,
            'status' => 'sold',
        ]);

        /*
         * TICKET SOLD
         */
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'sold' => 2,
        ]);

        /*
         * TICKET INSTANCES
         */
        $this->assertSame(
            2,
            TicketInstance::where(
                'order_id',
                $order->id
            )->count()
        );

        $this->assertSame(
            2,
            TicketInstance::where(
                'order_id',
                $order->id
            )
                ->where(
                    'status',
                    'issued'
                )
                ->count()
        );

        /*
         * Seat harus masuk ke TicketInstance.
         */
        $this->assertSame(
            2,
            TicketInstance::where(
                'order_id',
                $order->id
            )
                ->whereIn(
                    'seat_id',
                    [
                        $seatA01->id,
                        $seatA02->id,
                    ]
                )
                ->count()
        );

        /*
         * Tidak ada lagi OrderSeat reserved.
         */
        $this->assertSame(
            0,
            OrderSeat::where(
                'order_id',
                $order->id
            )
                ->where(
                    'status',
                    'reserved'
                )
                ->count()
        );
    }
}