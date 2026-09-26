<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSection;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EoSeatingController extends Controller
{
    private const MAX_GENERATED_SEATS = 10000;

    private const INSERT_CHUNK_SIZE = 1000;

    public function index(int $event)
    {
        $event = $this->ownedNumberedEvent($event);
        $sections = $event->sections()
            ->with('ticket')
            ->withCount([
                'seats',
                'seats as available_seats_count' => fn ($query) => $query->where('status', 'available'),
                'seats as reserved_seats_count' => fn ($query) => $query->where('status', 'reserved'),
                'seats as sold_seats_count' => fn ($query) => $query->where('status', 'sold'),
            ])
            ->with(['seats' => fn ($query) => $query
                ->withCount(['orderSeats', 'ticketInstance'])
                ->orderBy('row')
                ->orderBy('seat_code')])
            ->orderBy('code')
            ->get();

        return view('eo.events.seating.index', [
            'event' => $event,
            'sections' => $sections,
            'tickets' => $event->tickets()->orderBy('name')->get(),
            'canManageTopology' => $this->canManageTopology($event),
            'isReviewLocked' => $this->isReviewLocked($event),
        ]);
    }

    public function storeSection(Request $request, int $event)
    {
        $event = $this->ownedNumberedEvent($event);
        $code = strtoupper(trim((string) $request->input('code')));
        $request->merge(['code' => $code]);

        $validated = $request->validate([
            'code' => [
                'required', 'string', 'max:50', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('event_sections', 'code')->where('event_id', $event->id),
            ],
            'name' => ['required', 'string', 'max:100'],
            'ticket_id' => [
                'required', 'integer',
                Rule::exists('tickets', 'id')->where('event_id', $event->id),
            ],
        ]);

        try {
            DB::transaction(function () use ($event, $validated, $request) {
                $lockedEvent = $this->lockedOwnedNumberedEvent($request, $event->id);
                $this->assertCanManageTopology($lockedEvent);
                $ticket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($validated['ticket_id']);
                $this->assertTicketHasNoLegacyTransactions($ticket);

                $section = $lockedEvent->sections()->create([
                    'ticket_id' => $ticket->id,
                    'code' => $validated['code'],
                    'name' => trim($validated['name']),
                ]);
                $this->syncNumberedQuota($ticket);
            });
        } catch (QueryException $exception) {
            return back()->withInput()->withErrors(['code' => 'Kode section sudah digunakan dalam event ini.']);
        }

        return redirect()->route('eo.events.seating.index', $event)->with('success', 'Section berhasil ditambahkan.');
    }

    public function updateSection(Request $request, int $event, int $section)
    {
        $eventModel = $this->ownedNumberedEvent($event);
        $sectionModel = $eventModel->sections()->findOrFail($section);
        $code = strtoupper(trim((string) $request->input('code')));
        $request->merge(['code' => $code]);

        $validated = $request->validate([
            'code' => [
                'required', 'string', 'max:50', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('event_sections', 'code')->where('event_id', $eventModel->id)->ignore($sectionModel->id),
            ],
            'name' => ['required', 'string', 'max:100'],
            'ticket_id' => [
                'required', 'integer',
                Rule::exists('tickets', 'id')->where('event_id', $eventModel->id),
            ],
        ]);

        try {
            DB::transaction(function () use ($request, $event, $section, $validated) {
                $lockedEvent = $this->lockedOwnedNumberedEvent($request, $event);
                $this->assertCanManageTopology($lockedEvent);
                $lockedSection = $lockedEvent->sections()->lockForUpdate()->findOrFail($section);
                $oldTicket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($lockedSection->ticket_id);
                $newTicket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($validated['ticket_id']);

                if ((int) $newTicket->id !== (int) $oldTicket->id && $lockedSection->seats()->exists()) {
                    throw ValidationException::withMessages([
                        'ticket_id' => 'Section yang sudah memiliki kursi tidak dapat dipindahkan ke tipe tiket lain.',
                    ]);
                }

                $metadataChanged = $lockedSection->code !== $validated['code']
                    || $lockedSection->name !== trim($validated['name'])
                    || (int) $newTicket->id !== (int) $oldTicket->id;
                if ($metadataChanged && $this->sectionHasTransactionsOrOccupiedSeats($lockedSection)) {
                    throw ValidationException::withMessages([
                        'section' => 'Section dengan kursi yang sudah dipesan atau terjual tidak dapat diubah.',
                    ]);
                }

                $this->assertTicketHasNoLegacyTransactions($newTicket);
                $lockedSection->update([
                    'code' => $validated['code'],
                    'name' => trim($validated['name']),
                    'ticket_id' => $newTicket->id,
                ]);

                $lockedSection->seats()->update(['section' => trim($validated['name'])]);
                $this->syncNumberedQuota($oldTicket);
                if ($oldTicket->id !== $newTicket->id) {
                    $this->syncNumberedQuota($newTicket);
                }
            });
        } catch (QueryException $exception) {
            return back()->withInput()->withErrors(['code' => 'Kode section sudah digunakan dalam event ini.']);
        }

        return redirect()->route('eo.events.seating.index', $eventModel)->with('success', 'Section berhasil diperbarui.');
    }

    public function destroySection(Request $request, int $event, int $section)
    {
        $eventModel = $this->ownedNumberedEvent($event);
        DB::transaction(function () use ($request, $event, $section) {
            $lockedEvent = $this->lockedOwnedNumberedEvent($request, $event);
            $this->assertCanManageTopology($lockedEvent);
            $lockedSection = $lockedEvent->sections()->lockForUpdate()->findOrFail($section);
            $ticket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($lockedSection->ticket_id);

            if ($this->sectionHasTransactionsOrOccupiedSeats($lockedSection)) {
                throw ValidationException::withMessages([
                    'section' => 'Section tidak dapat dihapus karena memiliki kursi yang dipesan, terjual, atau terikat transaksi.',
                ]);
            }

            $lockedSection->seats()->delete();
            $lockedSection->delete();
            $this->syncNumberedQuota($ticket);
        });

        return redirect()->route('eo.events.seating.index', $eventModel)->with('success', 'Section dan kursi tersedia berhasil dihapus.');
    }

    public function generateSeats(Request $request, int $event, int $section)
    {
        $eventModel = $this->ownedNumberedEvent($event);
        $validated = $request->validate([
            'rows' => ['required', 'string', 'max:1000'],
            'seats_per_row' => ['required', 'integer', 'min:1', 'max:' . self::MAX_GENERATED_SEATS],
        ]);

        $rows = collect(explode(',', strtoupper($validated['rows'])))
            ->map(fn ($row) => trim($row))
            ->filter()
            ->unique()
            ->values();

        if ($rows->isEmpty() || $rows->contains(fn ($row) => strlen($row) > 30 || ! preg_match('/^[A-Z0-9-]+$/', $row))) {
            return back()->withInput()->withErrors(['rows' => 'Format baris tidak valid. Gunakan contoh: A, B, C.']);
        }

        $totalSeats = $rows->count() * (int) $validated['seats_per_row'];
        if ($totalSeats > self::MAX_GENERATED_SEATS) {
            return back()->withInput()->withErrors(['seats_per_row' => 'Maksimal 10.000 kursi dalam satu proses.']);
        }

        try {
            DB::transaction(function () use ($request, $event, $section, $rows, $validated, $totalSeats) {
                $lockedEvent = $this->lockedOwnedNumberedEvent($request, $event);
                $this->assertCanManageTopology($lockedEvent);
                $lockedSection = $lockedEvent->sections()->lockForUpdate()->findOrFail($section);
                $ticket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($lockedSection->ticket_id);
                $this->assertTicketHasNoLegacyTransactions($ticket);

                $padding = max(2, strlen((string) $validated['seats_per_row']));
                $seatData = [];
                foreach ($rows as $row) {
                    for ($number = 1; $number <= (int) $validated['seats_per_row']; $number++) {
                        $seatCode = $lockedSection->code . '-' . $row . str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
                        if (strlen($seatCode) > 100) {
                            throw ValidationException::withMessages([
                                'rows' => 'Kode kursi yang dihasilkan melebihi batas panjang yang didukung.',
                            ]);
                        }

                        $seatData[] = [
                            'event_id' => $lockedEvent->id,
                            'section_id' => $lockedSection->id,
                            'seat_code' => $seatCode,
                            'section' => $lockedSection->name,
                            'row' => $row,
                            'status' => 'available',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                $codes = collect($seatData)->pluck('seat_code');
                if ($lockedEvent->seats()->whereIn('seat_code', $codes)->exists()) {
                    throw ValidationException::withMessages([
                        'rows' => 'Kode kursi sudah digunakan dalam event ini. Gunakan row atau code section yang berbeda.',
                    ]);
                }

                foreach (array_chunk($seatData, self::INSERT_CHUNK_SIZE) as $chunk) {
                    Seat::insert($chunk);
                }

                $this->syncNumberedQuota($ticket);
            });
        } catch (QueryException $exception) {
            return back()->withInput()->withErrors(['rows' => 'Kode kursi bentrok dengan kursi yang sudah ada. Tidak ada perubahan disimpan.']);
        }

        return redirect()->route('eo.events.seating.index', $eventModel)->with('success', number_format($totalSeats) . ' kursi berhasil dibuat.');
    }

    public function destroySeat(Request $request, int $event, int $section, int $seat)
    {
        $eventModel = $this->ownedNumberedEvent($event);
        DB::transaction(function () use ($request, $event, $section, $seat) {
            $lockedEvent = $this->lockedOwnedNumberedEvent($request, $event);
            $this->assertCanManageTopology($lockedEvent);
            $lockedSection = $lockedEvent->sections()->lockForUpdate()->findOrFail($section);
            $ticket = $lockedEvent->tickets()->lockForUpdate()->findOrFail($lockedSection->ticket_id);
            $lockedSeat = $lockedSection->seats()->lockForUpdate()->findOrFail($seat);

            if (
                $lockedSeat->status !== 'available'
                || $lockedSeat->orderSeats()->exists()
                || $lockedSeat->ticketInstance()->exists()
            ) {
                throw ValidationException::withMessages([
                    'seat' => 'Kursi yang sudah dipesan atau terjual tidak dapat dihapus.',
                ]);
            }

            $lockedSeat->delete();
            $this->syncNumberedQuota($ticket);
        });

        return redirect()->route('eo.events.seating.index', $eventModel)->with('success', 'Kursi berhasil dihapus.');
    }

    private function ownedNumberedEvent(int $event): Event
    {
        $ownedEvent = Event::query()->where('user_id', auth()->id())->findOrFail($event);
        abort_unless($ownedEvent->seating_type === 'numbered_seat', 404);

        return $ownedEvent;
    }

    private function lockedOwnedNumberedEvent(Request $request, int $event): Event
    {
        $lockedEvent = Event::query()
            ->where('user_id', $request->user()->getKey())
            ->lockForUpdate()
            ->findOrFail($event);
        abort_unless($lockedEvent->seating_type === 'numbered_seat', 404);

        return $lockedEvent;
    }

    private function isReviewLocked(Event $event): bool
    {
        return $event->workflow_status === 'submitted' && $event->approval_status === 'pending';
    }

    private function canManageTopology(Event $event): bool
    {
        if ($this->isReviewLocked($event)) {
            return false;
        }

        if ($event->workflow_status === 'draft' || $event->approval_status === 'rejected') {
            return true;
        }

        if ($event->approval_status !== 'approved') {
            return false;
        }

        return ! $event->tickets()->whereHas('orders')->exists()
            && ! $event->seats()->whereIn('status', ['reserved', 'sold'])->exists();
    }

    private function assertCanManageTopology(Event $event): void
    {
        if ($this->isReviewLocked($event)) {
            throw ValidationException::withMessages([
                'seating' => 'Konfigurasi kursi dikunci karena event sedang dalam proses review.',
            ]);
        }

        if (! $this->canManageTopology($event)) {
            throw ValidationException::withMessages([
                'seating' => 'Konfigurasi kursi dikunci setelah event memiliki transaksi atau kursi terpakai.',
            ]);
        }
    }

    private function sectionHasTransactionsOrOccupiedSeats(EventSection $section): bool
    {
        return $section->seats()->whereIn('status', ['reserved', 'sold'])->exists()
            || $section->seats()->where(function ($query) {
                $query->whereHas('orderSeats')->orWhereHas('ticketInstance');
            })->exists();
    }

    private function assertTicketHasNoLegacyTransactions(Ticket $ticket): void
    {
        if ($ticket->orders()->exists() && ! $ticket->sections()->exists()) {
            throw ValidationException::withMessages([
                'ticket_id' => 'Ticket yang sudah memiliki transaksi lama tidak dapat dipetakan ke Section baru.',
            ]);
        }
    }

    private function syncNumberedQuota(Ticket $ticket): void
    {
        $assignedSeats = Seat::query()
            ->whereHas('section', fn ($query) => $query->where('ticket_id', $ticket->id))
            ->count();

        if ($assignedSeats < (int) $ticket->sold) {
            throw ValidationException::withMessages([
                'quota' => 'Kuota kursi tidak dapat lebih kecil dari jumlah tiket terjual.',
            ]);
        }

        $ticket->quota = $assignedSeats;
        $ticket->save();
    }
}
