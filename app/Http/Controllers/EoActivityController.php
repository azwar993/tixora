<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketInstance;
use Illuminate\Http\Request;

class EoActivityController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->getKey();
        $ownedEvent = fn ($query) => $query->where('events.user_id', $userId);

        $events = Event::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->latest('id')
            ->take(50)
            ->get(['id', 'name', 'created_at'])
            ->map(fn (Event $event) => [
                'type' => 'Event dibuat',
                'detail' => $event->name,
                'created_at' => $event->created_at,
            ]);

        $tickets = Ticket::query()
            ->whereHas('event', $ownedEvent)
            ->with('event:id,name')
            ->latest('created_at')
            ->latest('id')
            ->take(50)
            ->get(['id', 'event_id', 'name', 'created_at'])
            ->map(fn (Ticket $ticket) => [
                'type' => 'Ticket Type ditambahkan',
                'detail' => $ticket->name . ' · ' . $ticket->event->name,
                'created_at' => $ticket->created_at,
            ]);

        $orders = Order::query()
            ->whereHas('ticket.event', $ownedEvent)
            ->with('ticket.event:id,name')
            ->latest('created_at')
            ->latest('id')
            ->take(50)
            ->get(['id', 'ticket_id', 'order_code', 'created_at'])
            ->map(fn (Order $order) => [
                'type' => 'Order tercatat',
                'detail' => $order->order_code . ' · ' . $order->ticket->event->name,
                'created_at' => $order->created_at,
            ]);

        $issuedTickets = TicketInstance::query()
            ->whereIn('status', ['issued', 'used'])
            ->whereHas('ticket.event', $ownedEvent)
            ->with('ticket.event:id,name')
            ->latest('created_at')
            ->latest('id')
            ->take(50)
            ->get(['id', 'ticket_id', 'ticket_code', 'created_at'])
            ->map(fn (TicketInstance $ticketInstance) => [
                'type' => 'Tiket diterbitkan',
                'detail' => $ticketInstance->ticket_code . ' · ' . $ticketInstance->ticket->event->name,
                'created_at' => $ticketInstance->created_at,
            ]);

        $activities = $events
            ->concat($tickets)
            ->concat($orders)
            ->concat($issuedTickets)
            ->sortByDesc('created_at')
            ->take(50)
            ->values();

        return view('eo.activity.index', compact('activities'));
    }
}
