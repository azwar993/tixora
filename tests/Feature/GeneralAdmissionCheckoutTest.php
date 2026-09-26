<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneralAdmissionCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_admission_reservation_and_payment_flow(): void
    {
        /*
         * Mock Midtrans.
         *
         * Test tidak menghubungi Midtrans Sandbox sungguhan.
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

        /*
         * USER A
         */
        $userA = User::create([
            'name' => 'GA User A',
            'email' => 'ga-a@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        /*
         * USER B
         */
        $userB = User::create([
            'name' => 'GA User B',
            'email' => 'ga-b@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        /*
         * Verifikasi email.
         */
        $userA->update([
            'email_verified_at' => now(),
        ]);

        $userB->update([
            'email_verified_at' => now(),
        ]);

        /*
         * EVENT
         */
        $event = Event::create([
            'user_id' => $userA->id,
            'name' => 'GA Test Event',
            'category' => 'Music',
            'location' => 'Jakarta',
            'venue' => 'GA Test Venue',
            'seating_type' => 'general_admission',
            'event_date' => today()->addDays(7),
            'status' => 'coming_soon',
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'description' => 'General Admission integration test.',
            'image' => null,
        ]);

        /*
         * TICKET
         */
        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => 'GA Regular',
            'price' => 50000,
            'quota' => 100,
            'sold' => 0,
            'reserved' => 0,
            'description' => 'GA test ticket.',
        ]);

        /*
         * ==========================================================
         * USER A CHECKOUT 60 TICKET
         * ==========================================================
         */
        $responseA = $this
            ->actingAs($userA)
            ->postJson(route('checkout.store'), [
                'ticket_id' => $ticket->id,
                'quantity' => 60,
                'payment_method' => 'qris',
            ]);

        $responseA
            ->assertCreated()
            ->assertJsonPath(
                'order.quantity',
                60
            )
            ->assertJsonPath(
                'order.status',
                'pending'
            )
            ->assertJsonPath(
                'midtrans.snap_token',
                'test-snap-token'
            )
            ->assertJsonPath(
                'midtrans.client_key',
                'test-client-key'
            );

        /*
         * Pastikan reserved bertambah 60.
         */
        $ticket->refresh();

        $this->assertSame(
            0,
            $ticket->sold
        );

        $this->assertSame(
            60,
            $ticket->reserved
        );

        /*
         * Ambil order User A.
         */
        $orderA = Order::query()
            ->where('user_id', $userA->id)
            ->latest('id')
            ->firstOrFail();

        /*
         * Pastikan order pending.
         */
        $this->assertDatabaseHas('orders', [
            'id' => $orderA->id,
            'status' => 'pending',
            'quantity' => 60,
        ]);

        /*
         * Pastikan expires_at terisi.
         */
        $this->assertNotNull(
            $orderA->expires_at
        );

        /*
         * Pastikan payment pending.
         */
        $this->assertDatabaseHas('payments', [
            'order_id' => $orderA->id,
            'status' => 'pending',
            'payment_method' => 'qris',
            'amount' => '3000000.00',
        ]);

        /*
         * ==========================================================
         * USER B CHECKOUT 50 TICKET
         * ==========================================================
         *
         * Quota:
         *
         * 100 total
         * - 60 reserved
         * = 40 available
         *
         * User B meminta 50 → harus ditolak.
         */
        $responseB = $this
            ->actingAs($userB)
            ->postJson(route('checkout.store'), [
                'ticket_id' => $ticket->id,
                'quantity' => 50,
                'payment_method' => 'qris',
            ]);

        $responseB
            ->assertStatus(422)
            ->assertJsonValidationErrors('quantity');

        /*
         * Reserved tetap 60.
         */
        $ticket->refresh();

        $this->assertSame(
            0,
            $ticket->sold
        );

        $this->assertSame(
            60,
            $ticket->reserved
        );

        /*
         * Pastikan hanya satu order yang berhasil dibuat.
         */
        $this->assertSame(
            1,
            Order::count()
        );

        /*
         * ==========================================================
         * USER A SIMULATE PAYMENT
         * ==========================================================
         */
        $paymentResponse = $this
            ->actingAs($userA)
            ->postJson(
                route(
                    'checkout.simulate-payment',
                    $orderA
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
         * Refresh ticket setelah payment.
         */
        $ticket->refresh();

        /*
         * Reserved harus kembali 0.
         *
         * Sold harus menjadi tepat 60.
         */
        $this->assertSame(
            0,
            $ticket->reserved
        );

        $this->assertSame(
            60,
            $ticket->sold
        );

        /*
         * ==========================================================
         * ORDER PAID
         * ==========================================================
         */
        $this->assertDatabaseHas('orders', [
            'id' => $orderA->id,
            'status' => 'paid',
        ]);

        /*
         * ==========================================================
         * PAYMENT PAID
         * ==========================================================
         */
        $this->assertDatabaseHas('payments', [
            'order_id' => $orderA->id,
            'status' => 'paid',
        ]);

        $payment = Payment::query()
            ->where('order_id', $orderA->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertNotNull(
            $payment->paid_at
        );

        $this->assertNotNull(
            $payment->transaction_id
        );

        /*
         * ==========================================================
         * TICKET INSTANCE
         * ==========================================================
         */
        $this->assertSame(
            60,
            TicketInstance::where(
                'order_id',
                $orderA->id
            )->count()
        );

        /*
         * Semua ticket instance harus issued.
         */
        $this->assertSame(
            60,
            TicketInstance::where(
                'order_id',
                $orderA->id
            )
                ->where(
                    'status',
                    'issued'
                )
                ->count()
        );

        /*
         * ==========================================================
         * FINAL DATABASE STATE
         * ==========================================================
         *
         * 100 quota
         * 60 sold
         * 0 reserved
         * 40 available
         */
        $ticket->refresh();

        $this->assertSame(
            100,
            $ticket->quota
        );

        $this->assertSame(
            60,
            $ticket->sold
        );

        $this->assertSame(
            0,
            $ticket->reserved
        );

        /*
         * Hanya 1 order.
         */
        $this->assertSame(
            1,
            Order::count()
        );

        /*
         * Hanya 1 payment.
         */
        $this->assertSame(
            1,
            Payment::count()
        );

        /*
         * 60 ticket instance.
         */
        $this->assertSame(
            60,
            TicketInstance::count()
        );
    }
}