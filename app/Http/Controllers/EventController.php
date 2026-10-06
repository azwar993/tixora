<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Article;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketInstance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function home()
    {
        $events = Event::query()
            ->where('approval_status', 'approved')
            ->where('workflow_status', 'submitted')
            ->with(['tickets' => function ($query) {
                $query
                    ->whereColumn('sold', '<', 'quota')
                    ->orderBy('price');
            }])
            ->latest('event_date')
            ->get();

        $articles = Article::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('welcome', compact('events', 'articles'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $adminNotifications = collect();
        $adminNotificationCount = $adminNotifications->count();

        $events = Event::latest()
            ->with(['user', 'tickets'])
            ->get();

        $topEvents = Event::with([
            'tickets.orders.payments',
        ])
            ->get()
            ->map(function (Event $event) {
                $ticketsSold = $event->tickets->sum('sold');

                $revenue = $event->tickets
                    ->flatMap(fn ($ticket) => $ticket->orders)
                    ->flatMap(fn ($order) => $order->payments)
                    ->where('status', 'paid')
                    ->sum(fn ($payment) => (float) $payment->amount);

                return [
                    'event' => $event,
                    'ticketsSold' => $ticketsSold,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('ticketsSold')
            ->take(5)
            ->values();

        $tickets = Ticket::with('event')
            ->latest()
            ->get();

        $ticketsSold = Ticket::sum('sold');

        $recentOrders = Order::with([
            'user',
            'ticket.event',
        ])
            ->latest()
            ->take(5)
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

        $payments = Payment::with([
            'order.user',
            'order.ticket.event',
        ])
            ->latest()
            ->get();

        $totalPayments = Payment::count();

        $paidPayments = Payment::where('status', 'paid')->count();

        $revenue = Payment::where('status', 'paid')->sum('amount');

        $pendingPayments = Payment::where('status', 'pending')->count();

        $failedPayments = Payment::where('status', 'failed')->count();

        $reportPeriod = (string) $request->query('report_period', '30');
        if (! in_array($reportPeriod, ['7', '30', '90', 'all'], true)) {
            $reportPeriod = '30';
        }

        $reportStart = $reportPeriod === 'all'
            ? null
            : now()->subDays((int) $reportPeriod - 1)->startOfDay();

        $reportOrdersQuery = Order::query()
            ->when($reportStart, fn ($query) => $query->where('created_at', '>=', $reportStart));

        $reportPaymentsQuery = Payment::query()
            ->when($reportStart, fn ($query) => $query->where('created_at', '>=', $reportStart));

        $reportPaidPaymentsQuery = Payment::where('status', 'paid')
            ->when($reportStart, fn ($query) => $query->where('paid_at', '>=', $reportStart));

        $reportPaidOrders = Order::query()
            ->where('status', 'paid')
            ->whereHas('payments', function ($query) use ($reportStart) {
                $query->where('status', 'paid')
                    ->when($reportStart, fn ($query) => $query->where('paid_at', '>=', $reportStart));
            })
            ->with([
                'ticket.event',
                'payments' => function ($query) use ($reportStart) {
                    $query->where('status', 'paid')
                        ->when($reportStart, fn ($query) => $query->where('paid_at', '>=', $reportStart));
                },
            ])
            ->get();

        $reportEventPerformance = $reportPaidOrders
            ->groupBy(fn (Order $order) => $order->ticket?->event?->id)
            ->map(function ($orders) {
                $event = $orders->first()->ticket?->event;

                return [
                    'event' => $event,
                    'ticketsSold' => $orders->sum('quantity'),
                    'revenue' => $orders->sum(fn (Order $order) => $order->payments->sum('amount')),
                ];
            })
            ->filter(fn ($performance) => $performance['event'] !== null)
            ->sortByDesc('ticketsSold')
            ->take(5)
            ->values();

        $reportPaymentStatusCounts = $reportPaymentsQuery
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $reportOrderStatusCounts = (clone $reportOrdersQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $reportRevenue = (clone $reportPaidPaymentsQuery)->sum('amount');
        $reportTotalOrders = (clone $reportOrdersQuery)->count();
        $reportTicketsSold = $reportPaidOrders->sum('quantity');

        $reportEventsQuery = Event::query()
            ->when($reportStart, fn ($query) => $query->where('created_at', '>=', $reportStart));
        $reportUsersQuery = User::where('role', 'user')
            ->when($reportStart, fn ($query) => $query->where('created_at', '>=', $reportStart));

        $reportChartRevenue = (clone $reportPaidPaymentsQuery)
            ->whereNotNull('paid_at')
            ->select(DB::raw('DATE(paid_at) as report_date'), DB::raw('SUM(amount) as total'))
            ->groupBy('report_date')
            ->pluck('total', 'report_date');

        $reportChartOrders = (clone $reportOrdersQuery)
            ->select(DB::raw('DATE(created_at) as report_date'), DB::raw('COUNT(*) as total'))
            ->groupBy('report_date')
            ->pluck('total', 'report_date');

        $reportChartTickets = $reportPaidOrders
            ->groupBy(fn (Order $order) => $order->payments->first()?->paid_at?->toDateString())
            ->map(fn ($orders) => $orders->sum('quantity'))
            ->filter(fn ($total, $date) => $date !== null);

        $reportChartDates = $reportPeriod === 'all'
            ? $reportChartRevenue->keys()
                ->merge($reportChartOrders->keys())
                ->merge($reportChartTickets->keys())
                ->unique()
                ->sort()
                ->values()
            : collect(range(0, (int) $reportPeriod - 1))
                ->map(fn ($day) => now()->subDays((int) $reportPeriod - 1 - $day)->toDateString());

        $reportChartData = $reportChartDates->map(fn ($date) => [
            'date' => $date,
            'revenue' => (float) ($reportChartRevenue[$date] ?? 0),
            'orders' => (int) ($reportChartOrders[$date] ?? 0),
            'tickets_sold' => (int) ($reportChartTickets[$date] ?? 0),
        ])->values();

        $reportRecentOrders = (clone $reportOrdersQuery)
            ->with(['user', 'ticket.event'])
            ->latest()
            ->take(5)
            ->get();

        $reports = [
            'period' => $reportPeriod,
            'summary' => [
                'total_events' => (clone $reportEventsQuery)->count(),
                'total_users' => (clone $reportUsersQuery)->count(),
                'total_tickets_sold' => $reportTicketsSold,
                'total_paid_revenue' => $reportRevenue,
                'total_orders' => $reportTotalOrders,
                'payment_status_counts' => $reportPaymentStatusCounts,
                'order_status_counts' => $reportOrderStatusCounts,
            ],
            'event_performance' => $reportEventPerformance,
            'recent_orders' => $reportRecentOrders,
            'chart' => $reportChartData,
        ];

        return view('admin.dashboard', compact(
            'events',
            'topEvents',
            'tickets',
            'categories',
            'totalEvents',
            'eventsThisMonth',
            'totalUsers',
            'usersThisMonth',
            'ticketsSold',
            'revenue',
            'recentOrders',
            'payments',
            'totalPayments',
            'paidPayments',
            'pendingPayments',
            'failedPayments',
            'adminNotifications',
            'adminNotificationCount',
            'reportPeriod',
            'reports'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | USER DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function userDashboard(Request $request)
    {
        $userId = $request->user()->getKey();

        $activeTickets = TicketInstance::query()
            ->where('user_id', $userId)
            ->where('status', 'issued');

        $orders = Order::query()->where('user_id', $userId);

        $upcomingTickets = (clone $activeTickets)
            ->whereHas('ticket.event', function ($query) {
                $query->where('approval_status', 'approved')
                    ->whereDate('event_date', '>=', today());
            })
            ->with([
                'ticket:id,event_id,name',
                'ticket.event:id,name,event_date,location,venue,approval_status',
                'seat:id,seat_code,section,row',
            ])
            ->latest('created_at')
            ->paginate(6, ['*'], 'tickets_page')
            ->withQueryString();

        $upcomingEventCount = Event::query()
            ->where('approval_status', 'approved')
            ->whereDate('event_date', '>=', today())
            ->whereHas('tickets.ticketInstances', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', 'issued');
            })
            ->count();

        $recentOrders = (clone $orders)
            ->with(['ticket:id,event_id,name', 'ticket.event:id,name,event_date'])
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('dashboard', [
            'activeTicketCount' => (clone $activeTickets)->count(),
            'orderCount' => (clone $orders)->count(),
            'upcomingEventCount' => $upcomingEventCount,
            'upcomingTickets' => $upcomingTickets,
            'recentOrders' => $recentOrders,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | USER EVENT DETAIL
    |--------------------------------------------------------------------------
    */

public function show(Event $event)
{
    if ($event->approval_status !== 'approved' || $event->workflow_status !== 'submitted') {
        abort(404);
    }

    $event->load([
        'tickets' => function ($query) use ($event) {
            if ($event->seating_type !== 'numbered_seat') {
                $query->whereColumn('sold', '<', 'quota');
            }
            $query->orderBy('price');
        },
        'sections:id,event_id,ticket_id',
        'seats' => function ($query) {
            $query
                ->where('status', 'available')
                ->orderBy('section')
                ->orderBy('row')
                ->orderBy('seat_code');
        },
        'user',
    ]);

    $sectionTicketIds = $event->sections->pluck('ticket_id', 'id')->all();

    if ($event->seating_type === 'numbered_seat') {
        foreach ($event->tickets as $ticket) {
            $sectionIds = $event->sections
                ->where('ticket_id', $ticket->id)
                ->pluck('id');

            $hasSectionConfiguration = $sectionIds->isNotEmpty();
            $availableSeats = $hasSectionConfiguration
                ? $event->seats->whereIn('section_id', $sectionIds)->count()
                : max(0, $ticket->quota - $ticket->sold);

            $ticket->setAttribute('has_section_configuration', $hasSectionConfiguration);
            $ticket->setAttribute('available_seat_count', $availableSeats);
        }
    }

    return view('events.show', compact('event', 'sectionTicketIds'));
}

    /*
    |--------------------------------------------------------------------------
    | ADMIN EVENT MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'event_date' => 'required|date',
            'status' => 'required|in:coming_soon,on_going,past_event',
            'description' => 'nullable|string',
        ]);

        $imagePath = $request
            ->file('image')
            ->store('events', 'public');

        Event::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'location' => $validated['location'],
            'venue' => $validated['venue'],
            'event_date' => $validated['event_date'],
            'status' => $validated['status'],
            'workflow_status' => 'submitted',
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'event_date' => 'required|date',
            'status' => 'required|in:coming_soon,on_going,past_event',
            'description' => 'nullable|string',
        ]);

        $oldImagePath = $event->image;

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('events', 'public');
        } else {
            unset($validated['image']);
        }

        $event->update($validated);

        if (
            isset($validated['image'])
            && $oldImagePath
        ) {
            Storage::disk('public')->delete($oldImagePath);
        }

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

    /*
    |--------------------------------------------------------------------------
    | EVENT APPROVAL
    |--------------------------------------------------------------------------
    */

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
