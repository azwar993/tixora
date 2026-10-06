<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EoSalesController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->getKey();
        $paymentStatuses = Payment::query()
            ->whereHas('order.ticket.event', fn ($query) => $query->where('events.user_id', $userId))
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->all();

        $validated = $request->validate([
            'event_id' => [
                'nullable',
                'integer',
                Rule::exists('events', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'cancelled', 'expired'])],
            'payment_status' => ['nullable', Rule::in($paymentStatuses)],
        ]);

        $ownedEvents = Event::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $ordersQuery = Order::query()
            ->whereHas('ticket.event', fn ($query) => $query->where('events.user_id', $userId))
            ->when($validated['event_id'] ?? null, function ($query, $eventId) {
                $query->whereHas('ticket', fn ($ticketQuery) => $ticketQuery->where('event_id', $eventId));
            })
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['payment_status'] ?? null, function ($query, $status) {
                $query->whereHas('payments', fn ($paymentQuery) => $paymentQuery->where('status', $status));
            });

        $summary = [
            'orders' => (clone $ordersQuery)->count(),
            'tickets' => (int) (clone $ordersQuery)->sum('quantity'),
            'value' => (float) (clone $ordersQuery)->sum('total_price'),
        ];

        $orders = (clone $ordersQuery)
            ->with([
                'ticket.event:id,name',
                'payments' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('eo.sales.index', [
            'orders' => $orders,
            'ownedEvents' => $ownedEvents,
            'paymentStatuses' => $paymentStatuses,
            'summary' => $summary,
            'filters' => [
                'event_id' => $validated['event_id'] ?? '',
                'status' => $validated['status'] ?? '',
                'payment_status' => $validated['payment_status'] ?? '',
            ],
        ]);
    }
}