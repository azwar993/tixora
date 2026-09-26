<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('event')
            ->latest()
            ->get();

        $events = Event::latest()->get();

        return view('admin.tickets', compact('tickets', 'events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['sold'] = 0;

        Ticket::create($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Ticket berhasil ditambahkan.');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:' . $ticket->sold,
            'description' => 'nullable|string',
        ]);

        $ticket->update($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Ticket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->orders()->exists()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Ticket tidak dapat dihapus karena sudah memiliki order.');
        }

        $ticket->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Ticket berhasil dihapus.');
    }
}