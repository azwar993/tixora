@extends('layouts.eo', ['title' => 'Event Saya'])

@section('content')
<div class="eo-page eo-events-page">
    <section class="eo-events-intro">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Event Saya</h2>
            <p>Kelola dan pantau event yang kamu buat di TIXORA.</p>
        </div>
        <a class="eo-button eo-button-primary" href="{{ route('eo.events.create') }}">
            <i class="fa-solid fa-plus"></i><span>Buat Event</span>
        </a>
    </section>

    @if (session('success'))
        <div class="eo-form-notice is-success" role="status">{{ session('success') }}</div>
    @endif

    @php
        $filters = [
            'all' => 'Semua',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
        $approvalLabels = [
            'pending' => ['PENDING', 'Menunggu review Admin'],
            'approved' => ['APPROVED', 'Event telah disetujui'],
            'rejected' => ['REJECTED', 'Event ditolak'],
        ];
        $scheduleLabels = [
            'coming_soon' => 'COMING SOON',
            'on_going' => 'ON GOING',
            'past_event' => 'PAST EVENT',
        ];
    @endphp

    <nav class="eo-event-filters" aria-label="Filter status event">
        @foreach ($filters as $value => $label)
            @php $isActive = $value === 'all' ? $activeStatus === null : $activeStatus === $value; @endphp
            <a class="eo-event-filter {{ $isActive ? 'active' : '' }}"
               href="{{ $value === 'all' ? route('eo.events.index') : route('eo.events.index', ['status' => $value]) }}"
               @if ($isActive) aria-current="page" @endif>
                {{ $label }} <span>{{ $statusCounts[$value] }}</span>
            </a>
        @endforeach
    </nav>

    @if ($events->isEmpty())
        <section class="eo-panel eo-event-results">
            @if ($activeStatus !== null)
                <div class="eo-empty-state">
                    <span class="eo-empty-icon"><i class="fa-regular fa-calendar-xmark"></i></span>
                    <h3>Tidak ada event dengan status ini.</h3>
                    <p>Coba lihat semua event yang kamu miliki.</p>
                    <a class="eo-button eo-button-primary" href="{{ route('eo.events.index') }}">Semua Event</a>
                </div>
            @else
                <div class="eo-empty-state">
                    <span class="eo-empty-icon"><i class="fa-regular fa-calendar-plus"></i></span>
                    <h3>Belum ada event</h3>
                    <p>Event yang kamu buat akan muncul di sini.</p>
                    <a class="eo-button eo-button-primary" href="{{ route('eo.events.create') }}">
                        <i class="fa-solid fa-plus"></i><span>Buat Event</span>
                    </a>
                </div>
            @endif
        </section>
    @else
        <section class="eo-event-grid" aria-label="Event milik saya">
            @foreach ($events as $event)
                @php
                    $approval = $event->workflow_status === 'draft'
                        ? ['DRAFT', 'Draft tersimpan']
                        : ($approvalLabels[$event->approval_status] ?? [strtoupper((string) $event->approval_status), '']);
                    $ticketsSold = $event->tickets->sum('sold');
                    $ticketQuota = $event->tickets->sum('quota');
                @endphp
                <article class="eo-event-tile">
                    <div class="eo-event-tile-cover">
                        <span class="eo-event-tile-fallback" aria-hidden="true"><i class="fa-regular fa-image"></i></span>
                        @if ($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" alt="Cover {{ $event->name }}" loading="lazy"
                                 onerror="this.hidden=true">
                        @endif
                    </div>
                    <div class="eo-event-tile-body">
                        @if ($event->category)
                            <span class="eo-event-category">{{ $event->category }}</span>
                        @endif
                        <h3>{{ $event->name }}</h3>
                        <div class="eo-event-meta">
                            <span><i class="fa-regular fa-calendar"></i>{{ $event->event_date?->format('d M Y') ?? 'Tanggal belum tersedia' }}</span>
                            @if ($event->location || $event->venue)
                                <span><i class="fa-solid fa-location-dot"></i>{{ collect([$event->location, $event->venue])->filter()->implode(' · ') }}</span>
                            @endif
                        </div>
                        <div class="eo-event-badges">
                            <span class="eo-approval-badge {{ $event->workflow_status === 'draft' ? 'is-draft' : 'is-' . $event->approval_status }}" title="{{ $approval[1] }}">{{ $approval[0] }}</span>
                            @if (isset($scheduleLabels[$event->status]))
                                <span class="eo-schedule-badge">{{ $scheduleLabels[$event->status] }}</span>
                            @endif
                        </div>
                        @if ($event->approval_status === 'rejected' && filled($event->rejection_reason))
                            <p class="eo-rejection-reason"><strong>Alasan:</strong> {{ \Illuminate\Support\Str::limit($event->rejection_reason, 130) }}</p>
                        @endif
                        <div class="eo-event-tile-footer">
                            <span class="eo-ticket-summary"><i class="fa-solid fa-ticket"></i>{{ number_format($ticketsSold) }} / {{ number_format($ticketQuota) }} tiket</span>
                            <div class="eo-event-card-actions">
                                @if ($event->workflow_status === 'draft')
                                    <a class="eo-view-event" href="{{ route('eo.events.edit', $event) }}">Lanjutkan <i class="fa-solid fa-arrow-right"></i></a>
                                    <a class="eo-view-event" href="{{ route('eo.events.tickets.index', $event) }}">Kelola Tiket</a>
                                    @if ($event->seating_type === 'numbered_seat')
                                        <a class="eo-view-event" href="{{ route('eo.events.seating.index', $event) }}">Kelola Seating</a>
                                    @endif
                                @elseif ($event->approval_status === 'rejected')
                                    <a class="eo-view-event" href="{{ route('eo.events.edit', $event) }}">Perbaiki Event <i class="fa-solid fa-arrow-right"></i></a>
                                    <a class="eo-view-event" href="{{ route('eo.events.tickets.index', $event) }}">Kelola Tiket</a>
                                    @if ($event->seating_type === 'numbered_seat')
                                        <a class="eo-view-event" href="{{ route('eo.events.seating.index', $event) }}">Kelola Seating</a>
                                    @endif
                                @elseif ($event->approval_status === 'pending')
                                    <span class="eo-view-event is-unavailable" aria-disabled="true">Menunggu Review</span>
                                    <a class="eo-view-event" href="{{ route('eo.events.tickets.index', $event) }}">Lihat Ticket Types</a>
                                    @if ($event->seating_type === 'numbered_seat')
                                        <a class="eo-view-event" href="{{ route('eo.events.seating.index', $event) }}">Lihat Seating</a>
                                    @endif
                                @elseif ($event->approval_status === 'approved' && $event->workflow_status === 'submitted')
                                    <a class="eo-view-event" href="{{ route('events.show', $event) }}">Lihat Event <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    @if ($event->seating_type === 'numbered_seat')
                                        <a class="eo-view-event" href="{{ route('eo.events.seating.index', $event) }}">Lihat Seating</a>
                                    @endif
                                @else
                                    <span class="eo-view-event is-unavailable" aria-disabled="true">Belum Tersedia</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        @if ($events->hasPages())
            <div class="eo-pagination">{{ $events->links() }}</div>
        @endif
    @endif
</div>
@endsection
