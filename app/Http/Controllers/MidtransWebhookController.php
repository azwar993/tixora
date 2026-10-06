<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\MidtransService;
use App\Services\TicketIssuanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MidtransWebhookController extends Controller
{
    public function handle(
        Request $request,
        MidtransService $midtransService,
        TicketIssuanceService $ticketIssuanceService
    ): JsonResponse {
        $payload = $request->json()->all();

        /*
         * Pastikan field dasar tersedia.
         */
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if (
            $orderId === ''
            || $statusCode === ''
            || $grossAmount === ''
            || $signatureKey === ''
        ) {
            return response()->json([
                'message' => 'Invalid notification payload.',
            ], 400);
        }

        /*
         * Verifikasi signature dari Midtrans.
         *
         * SHA512(
         *     order_id
         *     + status_code
         *     + gross_amount
         *     + ServerKey
         * )
         */
        if (! $midtransService->verifyNotificationSignature(
            $orderId,
            $statusCode,
            $grossAmount,
            $signatureKey
        )) {
            return response()->json([
                'message' => 'Invalid Midtrans signature.',
            ], 403);
        }

        $transactionStatus =
            strtolower(
                (string) (
                    $payload['transaction_status']
                    ?? ''
                )
            );

        $fraudStatus =
            strtolower(
                (string) (
                    $payload['fraud_status']
                    ?? ''
                )
            );

        $paymentType =
            $payload['payment_type']
            ?? null;

        $transactionId =
            $payload['transaction_id']
            ?? null;

        /*
         * Cari order berdasarkan order_code Midtrans.
         */
        $order = Order::query()
            ->where('order_code', $orderId)
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Order not found.',
            ], 404);
        }

        $result = DB::transaction(function () use (
            $order,
            $payload,
            $transactionStatus,
            $fraudStatus,
            $paymentType,
            $transactionId,
            $grossAmount,
            $ticketIssuanceService
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->with([
                    'ticket.event',
                    'user',
                    'orderSeats.seat',
                ])
                ->lockForUpdate()
                ->firstOrFail();

            $payment = Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Payment untuk order tidak ditemukan.',
                ]);
            }

            /*
             * Pastikan nominal dari Midtrans sama dengan
             * nominal order kita.
             */
            if (
                (float) $grossAmount
                !== (float) $payment->amount
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Gross amount dari Midtrans tidak sesuai dengan amount order.',
                ]);
            }

            /*
             * Simpan transaction ID dan payment type
             * bila tersedia.
             */
            $paymentUpdate = [];

            if ($transactionId) {
                $paymentUpdate['transaction_id'] =
                    $transactionId;
            }

            if ($paymentType) {
                $paymentUpdate['payment_method'] =
                    $paymentType;
            }

            /*
             * ========================================================
             * SUCCESS
             * ========================================================
             *
             * settlement:
             *   sukses untuk kebanyakan payment method.
             *
             * capture:
             *   sukses untuk card transaction jika fraud accepted.
             */
            $isSuccessful =
                $transactionStatus === 'settlement'
                || (
                    $transactionStatus === 'capture'
                    && (
                        $fraudStatus === ''
                        || $fraudStatus === 'accept'
                    )
                );

            if ($isSuccessful) {
                /*
                 * Idempotency:
                 * kalau sudah paid, jangan issue tiket kedua kali.
                 */
                if (
                    $payment->status === 'paid'
                    && $lockedOrder->status === 'paid'
                ) {
                    if ($paymentUpdate !== []) {
                        $payment->update(
                            $paymentUpdate
                        );
                    }

                    return [
                        'status' => 'already_processed',
                        'order' => $lockedOrder->fresh([
                            'payments',
                            'orderSeats.seat',
                            'ticketInstances',
                        ]),
                    ];
                }

                /*
                 * Order expired/cancelled tidak boleh menerima
                 * settlement baru melalui webhook.
                 */
                if (
                    in_array(
                        $lockedOrder->status,
                        [
                            'expired',
                            'cancelled',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Order sudah expired atau cancelled.',
                    ]);
                }

                $paymentUpdate['status'] =
                    'paid';

                $paymentUpdate['paid_at'] =
                    $payment->paid_at
                    ?? now();

                $payment->update(
                    $paymentUpdate
                );

                $lockedOrder->update([
                    'status' => 'paid',
                ]);

                /*
                 * GENERAL ADMISSION
                 *
                 * reserved berkurang.
                 *
                 * sold akan dinaikkan oleh
                 * TicketIssuanceService setelah instance
                 * berhasil dibuat.
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

                $seatIds = $lockedOrder
                    ->orderSeats
                    ->where(
                        'status',
                        'reserved'
                    )
                    ->pluck('seat_id')
                    ->values()
                    ->all();

                /*
                 * Terbitkan ticket instance.
                 *
                 * Service memiliki idempotency check sendiri,
                 * sehingga notification duplicate tidak membuat
                 * tiket baru.
                 */
                $ticketIssuanceService->issue(
                    $lockedOrder,
                    $seatIds
                );

                return [
                    'status' => 'paid',
                    'order' => $lockedOrder->fresh([
                        'payments',
                        'orderSeats.seat',
                        'ticketInstances',
                    ]),
                ];
            }

            /*
             * ========================================================
             * PENDING
             * ========================================================
             */
            if ($transactionStatus === 'pending') {
                if (
                    $payment->status !== 'paid'
                    && $lockedOrder->status === 'pending'
                ) {
                    $paymentUpdate['status'] =
                        'pending';

                    $payment->update(
                        $paymentUpdate
                    );
                }

                return [
                    'status' => 'pending',
                    'order' => $lockedOrder->fresh([
                        'payments',
                    ]),
                ];
            }

            /*
             * ========================================================
             * FAILED / EXPIRED / CANCELLED
             * ========================================================
             */
            if (
                in_array(
                    $transactionStatus,
                    [
                        'deny',
                        'cancel',
                        'expire',
                        'failure',
                    ],
                    true
                )
            ) {
                /*
                 * Jangan mengubah transaksi yang sudah paid
                 * secara sembarangan.
                 */
                if (
                    $payment->status === 'paid'
                    || $lockedOrder->status === 'paid'
                ) {
                    if ($paymentUpdate !== []) {
                        $payment->update(
                            $paymentUpdate
                        );
                    }

                    return [
                        'status' =>
                            'already_paid',
                        'order' =>
                            $lockedOrder->fresh([
                                'payments',
                            ]),
                    ];
                }

                $paymentUpdate['status'] =
                    'failed';

                $payment->update(
                    $paymentUpdate
                );

                /*
                 * Lepaskan reservation GA.
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
                        >= $lockedOrder->quantity
                    ) {
                        $ticket->decrement(
                            'reserved',
                            $lockedOrder->quantity
                        );
                    }
                }

                /*
                 * Lepaskan numbered seat.
                 */
                foreach (
                    $lockedOrder->orderSeats
                        ->where(
                            'status',
                            'reserved'
                        )
                    as $orderSeat
                ) {
                    $seat = $orderSeat->seat;

                    if (
                        $seat
                        && $seat->status === 'reserved'
                    ) {
                        $seat->update([
                            'status' =>
                                'available',
                        ]);
                    }

                    $orderSeat->update([
                        'status' =>
                            'cancelled',
                    ]);
                }

                $lockedOrder->update([
                    'status' =>
                        $transactionStatus === 'expire'
                            ? 'expired'
                            : 'cancelled',
                ]);

                return [
                    'status' =>
                        $transactionStatus,
                    'order' =>
                        $lockedOrder->fresh([
                            'payments',
                            'orderSeats.seat',
                        ]),
                ];
            }

            /*
             * Status yang belum kita perlakukan.
             */
            return [
                'status' =>
                    $transactionStatus,
                'order' =>
                    $lockedOrder->fresh([
                        'payments',
                    ]),
            ];
        });

        /*
         * Midtrans hanya perlu menerima HTTP 200
         * setelah notification berhasil diproses.
         */
        return response()->json([
            'message' =>
                'Midtrans notification processed.',

            'status' =>
                $result['status'],
        ]);
    }
}