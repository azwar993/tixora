<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderSeat;
use App\Models\EventSection;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TicketIssuanceService
{
    public function issue(Order $order, array $seatIds = []): array
    {
        return DB::transaction(function () use ($order, $seatIds) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedOrder->load([
                'ticket.event',
                'user',
            ]);

            $this->validateOrder($lockedOrder);

            $paidPayment = $lockedOrder->payments()
                ->where('status', 'paid')
                ->latest('paid_at')
                ->latest('id')
                ->first();

            if (! $paidPayment) {
                throw ValidationException::withMessages([
                    'payment' => 'Order belum memiliki payment paid.',
                ]);
            }

            $existingInstances = $lockedOrder
                ->ticketInstances()
                ->get();

            $existingCount = $existingInstances->count();

            if ($existingCount > $lockedOrder->quantity) {
                throw ValidationException::withMessages([
                    'ticket_instances' => 'Jumlah TicketInstance melebihi quantity order.',
                ]);
            }

            if ($existingCount === $lockedOrder->quantity) {
                return $existingInstances->all();
            }

            $remaining = $lockedOrder->quantity - $existingCount;

            $ticket = Ticket::query()
                ->whereKey($lockedOrder->ticket_id)
                ->lockForUpdate()
                ->firstOrFail();

            $availableQuota = $ticket->quota - $ticket->sold;

            if ($remaining > $availableQuota) {
                throw ValidationException::withMessages([
                    'ticket' => 'Stok tiket tidak mencukupi untuk menerbitkan ticket instance.',
                ]);
            }

            $finalSeatIds = $this->prepareSeatsForIssuance(
                $lockedOrder,
                $remaining,
                $seatIds
            );

            $instances = [];

            foreach ($finalSeatIds as $seatId) {
                $instances[] = TicketInstance::create([
                    'order_id' => $lockedOrder->id,
                    'ticket_id' => $ticket->id,
                    'user_id' => $lockedOrder->user_id,
                    'seat_id' => $seatId,
                    'ticket_code' => $this->uniqueTicketCode(),
                    'qr_token' => $this->uniqueQrToken(),
                    'status' => 'issued',
                ]);
            }

            $ticket->increment('sold', count($instances));

            return $instances;
        });
    }

    private function validateOrder(Order $order): void
    {
        if ($order->status !== 'paid') {
            throw ValidationException::withMessages([
                'order' => 'Order belum berstatus paid.',
            ]);
        }

        if (! $order->ticket || ! $order->ticket->event || ! $order->user) {
            throw ValidationException::withMessages([
                'order' => 'Order tidak memiliki user, ticket, atau event yang valid.',
            ]);
        }

        if ($order->quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity order harus lebih besar dari nol.',
            ]);
        }
    }

    private function prepareSeatsForIssuance(
        Order $order,
        int $remaining,
        array $seatIds
    ): array {
        $event = $order->ticket->event;

        if ($event->seating_type !== 'numbered_seat') {
            if ($seatIds !== []) {
                throw ValidationException::withMessages([
                    'seat_id' => 'General Admission tidak boleh memiliki seat.',
                ]);
            }

            return array_fill(0, $remaining, null);
        }

        $orderSeats = OrderSeat::query()
            ->where('order_id', $order->id)
            ->where('status', 'reserved')
            ->lockForUpdate()
            ->get();

        if ($orderSeats->count() !== $remaining) {
            throw ValidationException::withMessages([
                'seat_id' => 'Alokasi seat untuk order tidak sesuai dengan ticket yang belum diterbitkan.',
            ]);
        }

        $reservedSeatIds = $orderSeats
            ->pluck('seat_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($seatIds !== []) {
            $requestedSeatIds = array_values(
                array_unique(
                    array_map('intval', $seatIds)
                )
            );

            sort($requestedSeatIds);
            sort($reservedSeatIds);

            if ($requestedSeatIds !== $reservedSeatIds) {
                throw ValidationException::withMessages([
                    'seat_id' => 'Seat yang dikirim tidak sesuai dengan seat yang direservasi oleh order.',
                ]);
            }
        }

        $seats = Seat::query()
            ->where('event_id', $event->id)
            ->whereIn('id', $reservedSeatIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($seats->count() !== count($reservedSeatIds)) {
            throw ValidationException::withMessages([
                'seat_id' => 'Sebagian seat tidak ditemukan pada event ticket.',
            ]);
        }

        foreach ($reservedSeatIds as $seatId) {
            $seat = $seats->get($seatId);

            if (! $seat || $seat->status !== 'reserved') {
                throw ValidationException::withMessages([
                    'seat_id' => 'Sebagian seat tidak lagi berstatus reserved.',
                ]);
            }

            if ($seat->section_id !== null) {
                $section = EventSection::query()->whereKey($seat->section_id)->first();
                if (! $section || (int) $section->event_id !== (int) $event->id || (int) $section->ticket_id !== (int) $order->ticket_id) {
                    throw ValidationException::withMessages([
                        'seat_id' => 'Seat yang direservasi tidak cocok dengan Ticket Type pada order.',
                    ]);
                }
            }
        }

        $mappedSeatCount = $seats->filter(fn ($seat) => $seat->section_id !== null)->count();
        if ($mappedSeatCount > 0 && $mappedSeatCount !== count($reservedSeatIds)) {
            throw ValidationException::withMessages([
                'seat_id' => 'Alokasi seat lama dan seat Section tidak dapat dicampur.',
            ]);
        }

        foreach ($orderSeats as $orderSeat) {
            $orderSeat->update([
                'status' => 'sold',
            ]);
        }

        foreach ($seats as $seat) {
            $seat->update([
                'status' => 'sold',
            ]);
        }

        return $reservedSeatIds;
    }

    private function uniqueTicketCode(): string
    {
        do {
            $code = 'TIX-' . strtoupper(Str::random(16));
        } while (
            TicketInstance::where('ticket_code', $code)->exists()
        );

        return $code;
    }

    private function uniqueQrToken(): string
    {
        do {
            $token = Str::random(64);
        } while (
            TicketInstance::where('qr_token', $token)->exists()
        );

        return $token;
    }
}
