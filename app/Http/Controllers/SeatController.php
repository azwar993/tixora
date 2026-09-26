<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeatController extends Controller
{
    private const MAX_GENERATED_SEATS = 10000;

    private const INSERT_CHUNK_SIZE = 1000;

    public function index(Request $request)
    {
        $events = Event::query()
            ->orderBy('name')
            ->get();

        $selectedEvent = null;
        $seats = collect();
        $seatSummary = collect();

        if ($request->filled('event_id')) {
            $selectedEvent = Event::query()
                ->findOrFail($request->integer('event_id'));

            $seatsQuery = Seat::where('event_id', $selectedEvent->id)
                ->orderBy('section')
                ->orderBy('row')
                ->orderBy('seat_code');

            $seatSummary = Seat::where('event_id', $selectedEvent->id)
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            if ($request->filled('search')) {
                $search = trim($request->input('search'));

                $seatsQuery->where(function ($query) use ($search) {
                    $query->where('seat_code', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%")
                        ->orWhere('row', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $seatsQuery->where('status', $request->input('status'));
            }

            $seats = $seatsQuery->get();
        }

        return view('admin.seats.index', compact('events', 'selectedEvent', 'seats', 'seatSummary'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'seat_code' => 'required|string|max:100',
            'section' => 'nullable|string|max:100',
            'row' => 'nullable|string|max:100',
            'status' => 'required|in:available,reserved,sold',
        ]);
        $validated['seat_code'] = trim($validated['seat_code']);
        $validated['section'] = $validated['section'] !== null ? trim($validated['section']) : null;
        $validated['row'] = $validated['row'] !== null ? trim($validated['row']) : null;

        $event = Event::findOrFail($validated['event_id']);

        if ($event->seating_type !== 'numbered_seat') {
            return back()
                ->withErrors(['event_id' => 'Seat hanya dapat ditambahkan untuk event dengan tipe Numbered Seat.'])
                ->withInput();
        }

        $seatExists = Seat::where('event_id', $event->id)
            ->where('seat_code', $validated['seat_code'])
            ->exists();

        if ($seatExists) {
            return back()
                ->withErrors(['seat_code' => 'Kode seat sudah digunakan pada event ini.'])
                ->withInput();
        }

        try {
            Seat::create($validated);
        } catch (QueryException $exception) {
            return back()
                ->withErrors(['seat_code' => 'Kode seat sudah digunakan pada event ini.'])
                ->withInput();
        }

        return redirect()
            ->route('admin.seats.index', ['event_id' => $event->id])
            ->with('success', 'Seat berhasil ditambahkan.');
    }

    public function update(Request $request, Seat $seat)
    {
        $validated = $request->validate([
            'seat_code' => 'required|string|max:100',
            'section' => 'nullable|string|max:100',
            'row' => 'nullable|string|max:100',
            'status' => 'required|in:available,reserved,sold',
        ]);
        $validated['seat_code'] = trim($validated['seat_code']);
        $validated['section'] = $validated['section'] !== null ? trim($validated['section']) : null;
        $validated['row'] = $validated['row'] !== null ? trim($validated['row']) : null;

        $seatExists = Seat::where('event_id', $seat->event_id)
            ->where('seat_code', $validated['seat_code'])
            ->where('id', '!=', $seat->id)
            ->exists();

        if ($seatExists) {
            return back()
                ->withErrors(['seat_code' => 'Kode seat sudah digunakan pada event ini.'])
                ->withInput();
        }

        try {
            $seat->update($validated);
        } catch (QueryException $exception) {
            return back()
                ->withErrors(['seat_code' => 'Kode seat sudah digunakan pada event ini.'])
                ->withInput();
        }

        return redirect()
            ->route('admin.seats.index', ['event_id' => $seat->event_id])
            ->with('success', 'Seat berhasil diperbarui.');
    }

    public function destroy(Seat $seat)
    {
        $eventId = $seat->event_id;
        $deleted = DB::transaction(function () use ($seat) {
            $lockedSeat = Seat::query()
                ->whereKey($seat->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSeat->status !== 'available') {
                return false;
            }

            $lockedSeat->delete();

            return true;
        });

        if (! $deleted) {
            return redirect()
                ->route('admin.seats.index', ['event_id' => $eventId])
                ->with('error', 'Seat reserved atau sold tidak dapat dihapus.');
        }

        return redirect()
            ->route('admin.seats.index', ['event_id' => $eventId])
            ->with('success', 'Seat berhasil dihapus.');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'integer',
                Rule::exists('events', 'id')->where(fn ($query) =>
                    $query->where('seating_type', 'numbered_seat')
                ),
            ],
            'section' => ['nullable', 'string', 'max:100'],
            'rows' => ['required', 'string', 'max:100'],
            'seats_per_row' => ['required', 'integer', 'min:1', 'max:' . self::MAX_GENERATED_SEATS],
        ]);

        $rows = collect(explode(',', strtoupper($validated['rows'])))
            ->map(fn ($row) => trim($row))
            ->filter()
            ->unique()
            ->values();

        if ($rows->isEmpty() || $rows->contains(fn ($row) => ! preg_match('/^[A-Z0-9-]+$/', $row))) {
            return back()
                ->withErrors(['rows' => 'Format baris tidak valid. Gunakan contoh: A, B, C.'])
                ->withInput();
        }

        $totalSeats = $rows->count() * $validated['seats_per_row'];

        if ($totalSeats > self::MAX_GENERATED_SEATS) {
            return back()
                ->withErrors([
                    'seats_per_row' => 'Jumlah seat yang akan dibuat adalah ' . number_format($totalSeats) .
                        '. Maksimal ' . number_format(self::MAX_GENERATED_SEATS) . ' seat dalam satu proses.',
                ])
                ->withInput();
        }

        $seatData = [];
        $seatCodes = [];

        foreach ($rows as $row) {
            for ($seatNumber = 1; $seatNumber <= $validated['seats_per_row']; $seatNumber++) {
                $seatCode = $row . str_pad((string) $seatNumber, 2, '0', STR_PAD_LEFT);
                $seatCodes[] = $seatCode;
                $seatData[] = [
                    'event_id' => $validated['event_id'],
                    'seat_code' => $seatCode,
                    'section' => $validated['section'] ?: null,
                    'row' => $row,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        try {
            DB::transaction(function () use ($validated, $seatCodes, $seatData) {
                $existingSeatCodes = collect();

                foreach (array_chunk($seatCodes, self::INSERT_CHUNK_SIZE) as $codeChunk) {
                    $existingSeatCodes = $existingSeatCodes->merge(
                        Seat::where('event_id', $validated['event_id'])
                            ->whereIn('seat_code', $codeChunk)
                            ->pluck('seat_code')
                    );
                }

                if ($existingSeatCodes->isNotEmpty()) {
                    $examples = $existingSeatCodes->take(5)->implode(', ');

                    throw ValidationException::withMessages([
                        'rows' => 'Seat sudah ada untuk event ini (' . $examples .
                            ($existingSeatCodes->count() > 5 ? ', ...' : '') .
                            '). Tidak ada seat baru yang dibuat.',
                    ]);
                }

                foreach (array_chunk($seatData, self::INSERT_CHUNK_SIZE) as $seatChunk) {
                    Seat::insert($seatChunk);
                }
            });
        } catch (QueryException $exception) {
            return back()
                ->withErrors([
                    'rows' => 'Seat tidak dapat dibuat karena kode seat bentrok dengan data yang sudah ada. Tidak ada perubahan yang disimpan.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('admin.seats.index', ['event_id' => $validated['event_id']])
            ->with('success', 'Sebanyak ' . number_format($totalSeats) . ' seat berhasil dibuat.');
    }

    public function updateType(Request $request, Event $event)
    {
        $validated = $request->validate([
            'seating_type' => ['required', 'in:general_admission,numbered_seat'],
        ]);

        $event->update($validated);

        return redirect()
            ->route('admin.seats.index', ['event_id' => $event->id])
            ->with('success', 'Tipe seating event berhasil diperbarui.');
    }
}
