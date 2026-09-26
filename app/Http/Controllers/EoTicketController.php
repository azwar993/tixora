<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EoTicketController extends Controller
{
    public function index(Event $event)
    {
        $event = $this->ownedEvent($event);

        $tickets = $event->tickets()->withCount('orders')->orderBy('id')->get();
        if ($event->seating_type === 'numbered_seat') {
            $tickets->each(function (Ticket $ticket) {
                $ticket->setAttribute('configured_seats_count', Seat::query()
                    ->whereHas('section', fn ($query) => $query->where('ticket_id', $ticket->id))
                    ->count());
                $ticket->setAttribute('available_seats_count', Seat::query()
                    ->where('status', 'available')
                    ->whereHas('section', fn ($query) => $query->where('ticket_id', $ticket->id))
                    ->count());
            });
        }

        return view('eo.events.tickets.index', [
            'event' => $event,
            'tickets' => $tickets,
            'isReviewLocked' => $this->isReviewLocked($event),
            'canCreate' => $this->canManageTicketTypes($event),
        ]);
    }

    public function create(Event $event)
    {
        $event = $this->ownedEvent($event);
        if (! $this->canManageTicketTypes($event)) {
            return redirect()
                ->route('eo.events.tickets.index', $event)
                ->with('error', $this->cannotCreateMessage($event));
        }

        return view('eo.events.tickets.create', [
            'event' => $event,
            'isNumberedSeat' => $event->seating_type === 'numbered_seat',
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $event = $this->ownedEvent($event);
        $validated = $request->validate($this->ticketRules($event));
        if ($event->seating_type === 'numbered_seat') {
            unset($validated['quota']);
        }

        $result = DB::transaction(function () use ($event, $validated) {
            $lockedEvent = $this->lockedOwnedEvent($event->id);
            if (! $this->canManageTicketTypes($lockedEvent)) {
                return ['error' => $this->cannotCreateMessage($lockedEvent)];
            }

            $lockedEvent->tickets()->create($validated);

            return ['success' => true];
        });

        if (isset($result['error'])) {
            return redirect()->route('eo.events.tickets.index', $event)->with('error', $result['error']);
        }

        return redirect()
            ->route('eo.events.tickets.index', $event)
            ->with('success', 'Ticket berhasil ditambahkan.');
    }

    public function edit(Event $event, Ticket $ticket)
    {
        $event = $this->ownedEvent($event);
        if ($this->isReviewLocked($event)) {
            return $this->lockedResponse($event);
        }

        $ticket = $event->tickets()->withCount('orders')->findOrFail($ticket->id);

        return view('eo.events.tickets.edit', [
            'event' => $event,
            'ticket' => $ticket,
            'isApproved' => $event->approval_status === 'approved',
            'isNumberedSeat' => $event->seating_type === 'numbered_seat',
            'configuredSeatsCount' => $event->seating_type === 'numbered_seat'
                ? Seat::query()->whereHas('section', fn ($query) => $query->where('ticket_id', $ticket->id))->count()
                : $ticket->quota,
            'availableSeatsCount' => $event->seating_type === 'numbered_seat'
                ? Seat::query()->where('status', 'available')
                    ->whereHas('section', fn ($query) => $query->where('ticket_id', $ticket->id))
                    ->count()
                : max(0, $ticket->quota - $ticket->sold - $ticket->reserved),
        ]);
    }

    public function update(Request $request, Event $event, Ticket $ticket)
    {
        $event = $this->ownedEvent($event);
        $validated = $request->validate($this->ticketRules($event));
        if ($event->seating_type === 'numbered_seat') {
            unset($validated['quota']);
        }

        $result = DB::transaction(function () use ($event, $ticket, $validated) {
            $lockedEvent = $this->lockedOwnedEvent($event->id);
            if ($this->isReviewLocked($lockedEvent)) {
                return ['error' => 'Ticket sedang dikunci karena event sedang dalam proses review.'];
            }

            $lockedTicket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($ticket->id);
            if ($lockedEvent->seating_type === 'numbered_seat') {
                unset($validated['quota']);
            }
            if (
                $lockedEvent->seating_type === 'general_admission'
                && (int) $validated['quota'] < ($lockedTicket->sold + $lockedTicket->reserved)
            ) {
                return ['validation' => 'Kuota tidak boleh lebih kecil dari tiket terjual atau reservasi.'];
            }

            if ($lockedEvent->approval_status === 'approved') {
                $priceChanged = number_format((float) $validated['price'], 2, '.', '')
                    !== number_format((float) $lockedTicket->price, 2, '.', '');

                if ($validated['name'] !== $lockedTicket->name || $priceChanged) {
                    return ['error' => 'Nama dan harga ticket tidak dapat diubah setelah event disetujui.'];
                }
            }

            $lockedTicket->update($validated);

            return ['success' => true];
        });

        if (isset($result['validation'])) {
            return back()->withErrors(['quota' => $result['validation']])->withInput();
        }
        if (isset($result['error'])) {
            return back()->withInput()->with('error', $result['error']);
        }

        return redirect()
            ->route('eo.events.tickets.index', $event)
            ->with('success', 'Ticket berhasil diperbarui.');
    }

    public function destroy(Event $event, Ticket $ticket)
    {
        $event = $this->ownedEvent($event);
        $result = DB::transaction(function () use ($event, $ticket) {
            $lockedEvent = $this->lockedOwnedEvent($event->id);
            if ($this->isReviewLocked($lockedEvent)) {
                return ['error' => 'Ticket sedang dikunci karena event sedang dalam proses review.'];
            }

            $lockedTicket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($ticket->id);
            if ($lockedTicket->sections()->exists()) {
                return ['error' => 'Hapus konfigurasi seating sebelum menghapus Ticket Type.'];
            }
            if ($lockedTicket->orders()->exists()) {
                return ['error' => 'Ticket tidak dapat dihapus karena sudah memiliki transaksi.'];
            }

            $lockedTicket->delete();

            return ['success' => true];
        });

        if (isset($result['error'])) {
            return redirect()
                ->route('eo.events.tickets.index', $event)
                ->with('error', $result['error']);
        }

        return redirect()
            ->route('eo.events.tickets.index', $event)
            ->with('success', 'Ticket berhasil dihapus.');
    }

    private function ownedEvent(Event $event): Event
    {
        return Event::query()
            ->where('user_id', auth()->id())
            ->findOrFail($event->id);
    }

    private function lockedOwnedEvent(int $eventId): Event
    {
        return Event::query()
            ->where('user_id', auth()->id())
            ->lockForUpdate()
            ->findOrFail($eventId);
    }

    private function isReviewLocked(Event $event): bool
    {
        return $event->workflow_status === 'submitted'
            && $event->approval_status === 'pending';
    }

    private function canManageTicketTypes(Event $event): bool
    {
        return ! $this->isReviewLocked($event)
            && ($event->workflow_status === 'draft' || $event->approval_status === 'rejected');
    }

    private function lockedResponse(Event $event)
    {
        return redirect()
            ->route('eo.events.tickets.index', $event)
            ->with('error', 'Ticket sedang dikunci karena event sedang dalam proses review.');
    }

    private function cannotCreateMessage(Event $event): string
    {
        if ($this->isReviewLocked($event)) {
            return 'Ticket sedang dikunci karena event sedang dalam proses review.';
        }

        return 'Ticket type baru tidak dapat ditambahkan setelah event disetujui.';
    }

    private function ticketRules(Event $event): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'description' => ['nullable', 'string'],
        ];

        if ($event->seating_type === 'general_admission') {
            $rules['quota'] = ['required', 'integer', 'min:1', 'max:4294967295'];
        }

        return $rules;
    }
}
