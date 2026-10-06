<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class EoDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $ownedEvents = Event::query()->where('user_id', $user->getKey());

        $totalEvents = (clone $ownedEvents)->count();
        $activeEvents = (clone $ownedEvents)
            ->where('approval_status', 'approved')
            ->where('status', 'on_going')
            ->count();
        $pendingEvents = (clone $ownedEvents)
            ->where('approval_status', 'pending')
            ->count();
        $approvedEvents = (clone $ownedEvents)
            ->where('approval_status', 'approved')
            ->count();
        $rejectedEvents = (clone $ownedEvents)
            ->where('approval_status', 'rejected')
            ->count();

        $ticketsSold = Ticket::query()
            ->whereHas('event', fn ($query) => $query->where('user_id', $user->getKey()))
            ->sum('sold');

        $paidRevenue = Payment::query()
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereHas('order', function ($query) use ($user) {
                $query->where('status', 'paid')
                    ->whereHas('ticket.event', fn ($eventQuery) => $eventQuery
                        ->where('user_id', $user->getKey()));
            })
            ->sum('amount');

        $eventCards = (clone $ownedEvents)
            ->with([
                'tickets:id,event_id,name,quota,sold',
            ])
            ->withCount('seats')
            ->latest('created_at')
            ->take(4)
            ->get()
            ->map(function (Event $event) {
                return [
                    'event' => $event,
                    'details_ready' => filled($event->name)
                        && filled($event->category)
                        && filled($event->location)
                        && filled($event->venue)
                        && filled($event->event_date),
                    'has_tickets' => $event->tickets->isNotEmpty(),
                    'tickets_sold' => $event->tickets->sum('sold'),
                    'ticket_quota' => $event->tickets->sum('quota'),
                    'seat_count' => $event->seats_count,
                ];
            });

        return view('eo.dashboard', compact(
            'user',
            'totalEvents',
            'activeEvents',
            'pendingEvents',
            'approvedEvents',
            'rejectedEvents',
            'ticketsSold',
            'paidRevenue',
            'eventCards'
        ));
    }
}
