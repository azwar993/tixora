<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSection;
use App\Models\Order;
use App\Models\OrderSeat;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Services\MidtransService;
use App\Services\TicketIssuanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    private const PAYMENT_METHODS = [
        'qris',
        'gopay',
        'shopeepay',
        'dana',
        'ovo',
        'bank_transfer',
        'va_mandiri',
        'va_bca',
        'va_bni',
        'va_bri',
        'debit',
    ];

    private const ORDER_EXPIRATION_MINUTES = 15;

    public function store(
        Request $request,
        MidtransService $midtransService
    ): JsonResponse {
        $validated = $request->validate([
            'ticket_id' => [
                'required',
                'integer',
                'exists:tickets,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'seat_ids' => [
                'nullable',
                'array',
            ],

            'seat_ids.*' => [
                'integer',
                'distinct',
            ],

            'payment_method' => [
                'required',
                'string',
                'in:' . implode(',', self::PAYMENT_METHODS),
            ],
        ]);

        $order = DB::transaction(function () use (
            $validated,
            $request
        ) {
            // Lock the parent Event first; EO topology changes use the same lock order.
            $ticketReference = Ticket::query()
                ->select(['id', 'event_id'])
                ->whereKey($validated['ticket_id'])
                ->firstOrFail();
            $lockedEvent = Event::query()
                ->whereKey($ticketReference->event_id)
                ->lockForUpdate()
                ->firstOrFail();
            $ticket = Ticket::query()
                ->with('event')
                ->whereKey($validated['ticket_id'])
                ->where('event_id', $lockedEvent->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateTicketForPurchase(
                $ticket,
                $validated['quantity']
            );

            $seatIds = array_values(
                $validated['seat_ids'] ?? []
            );

            $this->validateSeats(
                $ticket,
                $validated['quantity'],
                $seatIds
            );

            $totalPrice =
                (float) $ticket->price
                * $validated['quantity'];

            $order = Order::create([
                'order_code' => $this->uniqueOrderCode(),

                'user_id' => $request->user()->id,

                'ticket_id' => $ticket->id,

                'quantity' => $validated['quantity'],

                'total_price' => $totalPrice,

                'status' => 'pending',

                'expires_at' => now()->addMinutes(
                    self::ORDER_EXPIRATION_MINUTES
                ),
            ]);

            /*
             * GENERAL ADMISSION
             *
             * Pending order mengambil quota dari reserved.
             * Sold baru berubah setelah issuance berhasil.
             */
            if (
                $ticket->event->seating_type
                !== 'numbered_seat'
            ) {
                $ticket->increment(
                    'reserved',
                    $validated['quantity']
                );
            }

            /*
             * NUMBERED SEAT
             */
            if (
                $ticket->event->seating_type
                === 'numbered_seat'
            ) {
                foreach ($seatIds as $seatId) {
                    $seat = $ticket->event->seats()
                        ->whereKey($seatId)
                        ->lockForUpdate()
                        ->first();

                    if (
                        ! $seat
                        || $seat->status !== 'available'
                    ) {
                        throw ValidationException::withMessages([
                            'seat_ids' =>
                                'Seat sudah tidak tersedia.',
                        ]);
                    }

                    $seat->update([
                        'status' => 'reserved',
                    ]);

                    OrderSeat::create([
                        'order_id' => $order->id,

                        'seat_id' => $seat->id,

                        'status' => 'reserved',
                    ]);
                }
            }

            Payment::create([
                'order_id' => $order->id,

                'payment_code' =>
                    $this->uniquePaymentCode(),

                'transaction_id' => null,

                'payment_method' =>
                    $validated['payment_method'],

                'amount' => $totalPrice,

                'status' => 'pending',

                'paid_at' => null,
            ]);

            return $order->load([
                'ticket.event',
                'payments',
                'orderSeats.seat',
            ]);
        });

        /*
         * Create Midtrans Snap Token
         *
         * Order tetap pending.
         * Payment tetap pending.
         *
         * Midtrans akan menangani proses pembayaran.
         */
        $snapToken = $midtransService->createSnapToken(
            $order
        );

        return response()->json([
            'message' =>
                'Order berhasil dibuat dan siap dibayar.',

            'order' => [
                'id' => $order->id,

                'order_code' =>
                    $order->order_code,

                'ticket_id' =>
                    $order->ticket_id,

                'ticket_name' =>
                    $order->ticket->name,

                'event_id' =>
                    $order->ticket->event->id,

                'event_name' =>
                    $order->ticket->event->name,

                'quantity' =>
                    $order->quantity,

                'total_price' =>
                    $order->total_price,

                'status' =>
                    $order->status,

                'expires_at' =>
                    $order->expires_at?->toISOString(),

                'seats' =>
                    $order->orderSeats
                        ->map(function (
                            OrderSeat $orderSeat
                        ) {
                            return [
                                'order_seat_id' =>
                                    $orderSeat->id,

                                'seat_id' =>
                                    $orderSeat->seat_id,

                                'seat_code' =>
                                    $orderSeat
                                        ->seat
                                        ?->seat_code,

                                'status' =>
                                    $orderSeat->status,
                            ];
                        })
                        ->values()
                        ->all(),
            ],

            'payment' => $order->payments->first(),

            'midtrans' => [
                'client_key' =>
                    $midtransService->getClientKey(),

                'snap_token' =>
                    $snapToken,
            ],
        ], 201);
    }

    public function simulatePayment(
        Request $request,
        Order $order
    ): JsonResponse {
        $result = DB::transaction(function () use (
            $request,
            $order
        ) {
            $lockedOrder = Order::query()
                ->with([
                    'ticket.event',
                    'user',
                    'orderSeats.seat',
                ])
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Pastikan order milik user yang sedang login.
             */
            if (
                $lockedOrder->user_id
                !== $request->user()->id
            ) {
                abort(403);
            }

            if ($lockedOrder->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'order' =>
                        'Order sudah dibatalkan.',
                ]);
            }

            if ($lockedOrder->status === 'expired') {
                throw ValidationException::withMessages([
                    'order' =>
                        'Order sudah expired.',
                ]);
            }

            /*
             * Event tidak boleh dibayar setelah tanggal event.
             */
            if (
                $lockedOrder->ticket?->event?->event_date
                && $lockedOrder
                    ->ticket
                    ->event
                    ->event_date
                    ->isBefore(today())
            ) {
                throw ValidationException::withMessages([
                    'event' =>
                        'Event sudah berlalu.',
                ]);
            }

            /*
             * Pending order yang sudah melewati expiry
             * tidak boleh dibayar.
             */
            if (
                $lockedOrder->status === 'pending'
                && $lockedOrder->expires_at
                && $lockedOrder->expires_at->isPast()
            ) {
                throw ValidationException::withMessages([
                    'order' =>
                        'Waktu pembayaran order sudah habis.',
                ]);
            }

            $payment = $lockedOrder->payments()
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Payment untuk order tidak ditemukan.',
                ]);
            }

            if ($payment->status === 'failed') {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Payment yang gagal tidak dapat diubah menjadi paid.',
                ]);
            }

            /*
             * PAYMENT PENDING → PAID
             */
            if ($payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',

                    'transaction_id' =>
                        $this->uniqueTransactionId(),

                    'paid_at' => now(),
                ]);

                $lockedOrder->update([
                    'status' => 'paid',
                ]);

                /*
                 * GENERAL ADMISSION
                 *
                 * reserved → 0
                 *
                 * sold tidak diubah di sini.
                 * TicketIssuanceService yang menaikkannya
                 * setelah TicketInstance berhasil dibuat.
                 */
                if (
                    $lockedOrder
                        ->ticket
                        ->event
                        ->seating_type
                        !== 'numbered_seat'
                ) {
                    $ticket = Ticket::query()
                        ->whereKey(
                            $lockedOrder->ticket_id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (
                        $ticket->reserved
                        < $lockedOrder->quantity
                    ) {
                        throw ValidationException::withMessages([
                            'ticket' =>
                                'Reserved quota tidak mencukupi.',
                        ]);
                    }

                    $ticket->decrement(
                        'reserved',
                        $lockedOrder->quantity
                    );
                }
            }

            if ($payment->status !== 'paid') {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Status payment tidak valid untuk proses issuance.',
                ]);
            }

            /*
             * Ambil seat yang masih reserved
             * oleh order ini.
             */
            $seatIds = $lockedOrder
                ->orderSeats
                ->where('status', 'reserved')
                ->pluck('seat_id')
                ->values()
                ->all();

            $ticketInstances = app(
                TicketIssuanceService::class
            )->issue(
                $lockedOrder,
                $seatIds
            );

            return [
                'order' =>
                    $lockedOrder->fresh([
                        'ticket.event',
                        'payments',
                        'orderSeats.seat',
                    ]),

                'ticket_instances' =>
                    $ticketInstances,
            ];
        });

        return response()->json([
            'message' =>
                'Pembayaran berhasil dan tiket berhasil diterbitkan.',

            'order' => [
                'id' =>
                    $result['order']->id,

                'order_code' =>
                    $result['order']->order_code,

                'status' =>
                    $result['order']->status,

                'total_price' =>
                    $result['order']->total_price,
            ],

            'ticket_instances' =>
                collect(
                    $result['ticket_instances']
                )
                    ->map(
                        function (
                            TicketInstance $ticketInstance
                        ) {
                            return [
                                'id' =>
                                    $ticketInstance->id,

                                'ticket_code' =>
                                    $ticketInstance->ticket_code,

                                'qr_token' =>
                                    $ticketInstance->qr_token,

                                'status' =>
                                    $ticketInstance->status,

                                'seat_id' =>
                                    $ticketInstance->seat_id,
                            ];
                        }
                    )
                    ->values()
                    ->all(),
        ]);
    }

    private function validateTicketForPurchase(
        Ticket $ticket,
        int $quantity
    ): void {
        $event = $ticket->event;

        if (! $event) {
            throw ValidationException::withMessages([
                'ticket_id' =>
                    'Event ticket tidak ditemukan.',
            ]);
        }

        if ($event->approval_status !== 'approved') {
            throw ValidationException::withMessages([
                'event' =>
                    'Event belum tersedia untuk pembelian.',
            ]);
        }

        if ($event->status === 'past_event') {
            throw ValidationException::withMessages([
                'event' =>
                    'Event sudah berlalu.',
            ]);
        }

        if (
            $event->event_date
            && $event->event_date->isBefore(today())
        ) {
            throw ValidationException::withMessages([
                'event' =>
                    'Event sudah berlalu.',
            ]);
        }

        if ($ticket->price === null) {
            throw ValidationException::withMessages([
                'ticket_id' =>
                    'Harga ticket belum tersedia.',
            ]);
        }

        /*
         * GENERAL ADMISSION
         *
         * available =
         * quota - sold - reserved
         */
        if (
            $event->seating_type
            !== 'numbered_seat'
        ) {
            $remaining =
                $ticket->quota
                - $ticket->sold
                - $ticket->reserved;

            if ($quantity > $remaining) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'Jumlah tiket melebihi stok yang tersedia.',
                ]);
            }

            return;
        }

        // Numbered inventory is validated against the selected seats below.
    }

    private function validateSeats(
        Ticket $ticket,
        int $quantity,
        array $seatIds
    ): void {
        $event = $ticket->event;

        /*
         * GENERAL ADMISSION
         */
        if (
            $event->seating_type
            !== 'numbered_seat'
        ) {
            if ($seatIds !== []) {
                throw ValidationException::withMessages([
                    'seat_ids' =>
                        'General Admission tidak menggunakan numbered seat.',
                ]);
            }

            return;
        }

        /*
         * NUMBERED SEAT
         */
        if (count($seatIds) !== $quantity) {
            throw ValidationException::withMessages([
                'seat_ids' =>
                    'Numbered Seat harus memilih satu seat untuk setiap tiket.',
            ]);
        }

        $seats = $event->seats()
            ->whereIn('id', $seatIds)
            ->lockForUpdate()
            ->get();

        if (
            $seats->count()
            !== count($seatIds)
        ) {
            throw ValidationException::withMessages([
                'seat_ids' =>
                    'Sebagian seat tidak ditemukan pada event ini.',
            ]);
        }

        foreach ($seats as $seat) {
            if ($seat->status !== 'available') {
                throw ValidationException::withMessages([
                    'seat_ids' =>
                        'Sebagian seat sudah tidak tersedia.',
                ]);
            }
        }

        $sectionsBySeatId = EventSection::query()
            ->join('seats', 'seats.section_id', '=', 'event_sections.id')
            ->whereIn('seats.id', $seatIds)
            ->get([
                'seats.id as seat_id',
                'event_sections.id',
                'event_sections.event_id',
                'event_sections.ticket_id',
            ])
            ->keyBy('seat_id');
        $mappedCount = $sectionsBySeatId->count();
        if ($mappedCount > 0 && $mappedCount !== $seats->count()) {
            throw ValidationException::withMessages([
                'seat_ids' => 'Seat lama dan seat Section tidak dapat dicampur dalam satu order.',
            ]);
        }

        if ($mappedCount === 0) {
            // Keep the original inventory behavior for legacy seats without a Section.
            if ($quantity > ($ticket->quota - $ticket->sold)) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah tiket melebihi stok yang tersedia.',
                ]);
            }

            return;
        }

        foreach ($seats as $seat) {
            $section = $sectionsBySeatId->get($seat->id);

            if (! $section || (int) $section->event_id !== (int) $event->id || (int) $section->ticket_id !== (int) $ticket->id) {
                throw ValidationException::withMessages([
                    'seat_ids' => 'Semua kursi harus berasal dari Ticket Type yang dipilih.',
                ]);
            }
        }

        $availableSeats = Seat::query()
            ->where('status', 'available')
            ->whereHas('section', fn ($query) => $query
                ->where('event_id', $event->id)
                ->where('ticket_id', $ticket->id))
            ->count();

        if ($quantity > $availableSeats) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah tiket melebihi kursi yang tersedia untuk Ticket Type ini.',
            ]);
        }
    }

    private function uniqueOrderCode(): string
    {
        do {
            $code =
                'ORD-'
                . strtoupper(
                    Str::random(16)
                );
        } while (
            Order::where(
                'order_code',
                $code
            )->exists()
        );

        return $code;
    }

    private function uniquePaymentCode(): string
    {
        do {
            $code =
                'PAY-'
                . strtoupper(
                    Str::random(16)
                );
        } while (
            Payment::where(
                'payment_code',
                $code
            )->exists()
        );

        return $code;
    }

    private function uniqueTransactionId(): string
    {
        do {
            $transactionId =
                'SIM-'
                . strtoupper(
                    Str::random(20)
                );
        } while (
            Payment::where(
                'transaction_id',
                $transactionId
            )->exists()
        );

        return $transactionId;
    }
}
