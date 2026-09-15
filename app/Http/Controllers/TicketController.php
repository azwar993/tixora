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
            'quota' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Ticket::create($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Ticket berhasil ditambahkan.');
    }
}