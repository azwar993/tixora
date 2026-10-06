<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketInstance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EoParticipantsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->getKey();

        $validated = $request->validate([
            'event_id' => [
                'nullable',
                'integer',
                Rule::exists('events', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'status' => ['nullable', Rule::in(['issued', 'used'])],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $ownedEvents = Event::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $participantsQuery = TicketInstance::query()
            ->whereIn('status', ['issued', 'used'])
            ->whereHas('ticket.event', fn ($query) => $query->where('events.user_id', $userId))
            ->when($validated['event_id'] ?? null, function ($query, $eventId) {
                $query->whereHas('ticket', fn ($ticketQuery) => $ticketQuery->where('event_id', $eventId));
            })
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->whereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhere('ticket_code', 'like', '%' . $search . '%');
                });
            });

        $summary = [
            'participants' => (clone $participantsQuery)->distinct()->count('user_id'),
            'tickets' => (clone $participantsQuery)->count(),
        ];

        $participants = (clone $participantsQuery)
            ->with([
                'user:id,name,email',
                'ticket:id,event_id,name',
                'ticket.event:id,name',
                'seat:id,seat_code',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('eo.participants.index', [
            'participants' => $participants,
            'ownedEvents' => $ownedEvents,
            'summary' => $summary,
            'filters' => [
                'event_id' => $validated['event_id'] ?? '',
                'status' => $validated['status'] ?? '',
                'search' => $validated['search'] ?? '',
            ],
        ]);
    }
}