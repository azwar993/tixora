<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
{
    $events = Event::latest()->get();

    $tickets = Ticket::with('event')
        ->latest()
        ->get();

    $categories = Category::latest()->get();

    $totalEvents = Event::count();

    $eventsThisMonth = Event::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $totalUsers = User::where('role', 'user')->count();

    $usersThisMonth = User::where('role', 'user')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    return view('admin.dashboard', compact(
        'events',
        'tickets',
        'categories',
        'totalEvents',
        'eventsThisMonth',
        'totalUsers',
        'usersThisMonth'
    ));
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

        Event::create([
            ...$validated,

            // Event yang dibuat langsung oleh Admin
            // otomatis dianggap sudah disetujui.
            'user_id' => auth()->id(),
            'approval_status' => 'approved',
            'rejection_reason' => null,
        ]);

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

    public function approve(Event $event)
    {
        $event->update([
            'approval_status' => 'approved',
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event berhasil di-approve.');
    }

    public function reject(Request $request, Event $event)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $event->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event berhasil ditolak.');
    }
}