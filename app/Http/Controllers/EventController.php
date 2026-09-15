<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();

        return view('admin.dashboard', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'event_date' => 'required|date',
            'status' => 'required|in:coming_soon,on_going,past_event',
            'description' => 'nullable|string',
        ]);

        Event::create($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'event_date' => 'required|date',
            'status' => 'required|in:coming_soon,on_going,past_event',
            'description' => 'nullable|string',
        ]);

        $event->update($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event berhasil dihapus.');
    }
}