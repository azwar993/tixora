<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use RuntimeException;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    private function configure(): void
    {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            throw new RuntimeException(
                'MIDTRANS_SERVER_KEY belum dikonfigurasi.'
            );
        }

        Config::$serverKey = $serverKey;

        Config::$isProduction = (bool) config(
            'midtrans.is_production',
            false
        );

        Config::$isSanitized = (bool) config(
            'midtrans.is_sanitized',
            true
        );

        Config::$is3ds = (bool) config(
            'midtrans.is_3ds',
            true
        );
    }

    public function getClientKey(): string
    {
        $clientKey = config('midtrans.client_key');

        if (! $clientKey) {
            throw new RuntimeException(
                'MIDTRANS_CLIENT_KEY belum dikonfigurasi.'
            );
        }

        return $clientKey;
    }

    public function createSnapToken(Order $order): string
    {
        $order->load([
            'user',
            'ticket.event',
        ]);

        if (! $order->user) {
            throw new RuntimeException(
                'User order tidak ditemukan.'
            );
        }

        if (! $order->ticket) {
            throw new RuntimeException(
                'Ticket order tidak ditemukan.'
            );
        }

        if (! $order->ticket->event) {
            throw new RuntimeException(
                'Event ticket tidak ditemukan.'
            );
        }

        if ($order->status !== 'pending') {
            throw new RuntimeException(
                'Snap token hanya dapat dibuat untuk order pending.'
            );
        }

        if (
            $order->expires_at
            && $order->expires_at->isPast()
        ) {
            throw new RuntimeException(
                'Order sudah expired.'
            );
        }

        $amount = (int) round(
            (float) $order->total_price
        );

        if ($amount < 1) {
            throw new RuntimeException(
                'Total pembayaran tidak valid.'
            );
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_code,
                'gross_amount' => $amount,
            ],

            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],

            'item_details' => [
                [
                    'id' => (string) $order->ticket_id,
                    'price' => (int) round(
                        (float) $order->ticket->price
                    ),
                    'quantity' => $order->quantity,
                    'name' => $order->ticket->name,
                ],
            ],

            'custom_field1' => (string) $order->id,
            'custom_field2' => (string) $order->ticket->event->id,
            'custom_field3' => $order->order_code,
        ];

        return Snap::getSnapToken($params);
    }

    public function verifyNotificationSignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey
    ): bool {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            return false;
        }

        $rawSignature =
            $orderId
            . $statusCode
            . $grossAmount
            . $serverKey;

        $expectedSignature = hash(
            'sha512',
            $rawSignature
        );

        return hash_equals(
            $expectedSignature,
            $signatureKey
        );
    }
}