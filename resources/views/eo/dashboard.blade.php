@extends('layouts.eo', ['title' => 'Overview'])

@section('content')
<div class="eo-page">
    @if (session('success'))
        <div class="eo-form-notice is-success" role="status">{{ session('success') }}</div>
    @endif

    <section class="eo-greeting">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Halo, {{ $user->name }}</h2>
            <p>Kelola event kamu di TIXORA.</p>
        </div>
    </section>

    <div class="eo-overview-meta">
        <span><strong>{{ number_format($totalEvents) }}</strong> total event</span>
        <span class="eo-meta-dot"></span>
        <span><strong>{{ number_format($approvedEvents) }}</strong> approved</span>
        <span class="eo-meta-dot"></span>
        <span><strong>{{ number_format($rejectedEvents) }}</strong> rejected</span>
    </div>

    <section class="eo-stats-grid" aria-label="Ringkasan event dan penjualan">
        <article class="eo-stat-card">
            <div class="eo-stat-icon purple"><i class="fa-solid fa-bolt"></i></div>
            <div><span>EVENT AKTIF</span><strong>{{ number_format($activeEvents) }}</strong><small>Approved dengan jadwal sedang berlangsung</small></div>
        </article>
        <article class="eo-stat-card">
            <div class="eo-stat-icon amber"><i class="fa-regular fa-clock"></i></div>
            <div><span>MENUNGGU REVIEW</span><strong>{{ number_format($pendingEvents) }}</strong><small>Menunggu pemeriksaan Admin</small></div>
        </article>
        <article class="eo-stat-card">
            <div class="eo-stat-icon blue"><i class="fa-solid fa-ticket"></i></div>
            <div><span>TIKET TERJUAL</span><strong>{{ number_format($ticketsSold) }}</strong><small>Total dari tiket event milikmu</small></div>
        </article>
    </section>

    <div class="eo-content-grid">
        <section class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div>
                    <span class="eo-eyebrow">EVENT MILIKMU</span>
                    <h2>Event Kamu</h2>
                </div>
                <a class="eo-count-pill eo-all-events-link" href="{{ route('eo.events.index') }}">{{ number_format($totalEvents) }} event <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            @if ($eventCards->isEmpty())
                <div class="eo-empty-state">
                    <span class="eo-empty-icon"><i class="fa-regular fa-calendar-plus"></i></span>
                    <h3>Belum ada event</h3>
                    <p>Mulai buat event pertama kamu di TIXORA.</p>
                    <a class="eo-button eo-button-primary" href="{{ route('eo.events.create') }}">
                        <i class="fa-solid fa-plus"></i><span>Buat Event</span>
                    </a>
                </div>
            @else
                <div class="eo-event-list">
                    @foreach ($eventCards as $card)
                        @php
                            $event = $card['event'];
                            $approvalLabels = [
                                'draft' => 'Draft',
                                'pending' => 'Menunggu review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ];
                            $scheduleLabels = [
                                'coming_soon' => 'COMING SOON',
                                'on_going' => 'ON GOING',
                                'past_event' => 'PAST EVENT',
                            ];
                        @endphp
                        <article class="eo-event-card">
                            <div class="eo-event-cover eo-event-cover-placeholder">
                                <span class="eo-cover-fallback" aria-hidden="true"><i class="fa-regular fa-image"></i></span>
                                @if ($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="Cover {{ $event->name }}" loading="lazy" onerror="this.hidden=true">
                                @endif
                            </div>

                            <div class="eo-event-content">
                                <div class="eo-event-heading">
                                    <div class="eo-event-title-group">
                                        <span class="eo-event-category">{{ $event->category }}</span>
                                        <h3>{{ $event->name }}</h3>
                                    </div>
                                    <span class="eo-approval-badge {{ 'is-' . $event->approval_status }}">{{ $approvalLabels[$event->approval_status] ?? ucfirst($event->approval_status) }}</span>
                                </div>

                                <div class="eo-event-meta">
                                    <span><i class="fa-regular fa-calendar"></i>{{ $event->event_date?->format('d M Y') ?? 'Tanggal belum tersedia' }}</span>
                                    <span><i class="fa-solid fa-location-dot"></i>{{ $event->location }} · {{ $event->venue }}</span>
                                </div>

                                <div class="eo-event-summary">
                                    <span><i class="fa-solid fa-ticket"></i>{{ number_format($card['tickets_sold']) }} / {{ number_format($card['ticket_quota']) }} tiket</span>
                                    @if (isset($scheduleLabels[$event->status]))
                                        <span class="eo-schedule-badge">{{ $scheduleLabels[$event->status] }}</span>
                                    @endif
                                </div>
                                <a class="eo-manage-event-link" href="{{ route('eo.events.index') }}">Kelola Event <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="eo-panel eo-health-panel">
            <div class="eo-panel-heading">
                <div>
                    <span class="eo-eyebrow">RINGKASAN KESIAPAN</span>
                    <h2>Event Health</h2>
                </div>
                <span class="eo-health-mark"><i class="fa-solid fa-heart-pulse"></i></span>
            </div>

            @if ($eventCards->isEmpty())
                <div class="eo-health-empty">Status kesiapan akan tampil setelah kamu memiliki event.</div>
            @else
                <div class="eo-health-list">
                    @foreach ($eventCards as $card)
                        @php $event = $card['event']; @endphp
                        <article class="eo-health-event">
                            <h3>{{ $event->name }}</h3>
                            <ul>
                                <li class="{{ $card['details_ready'] ? 'is-ready' : 'is-pending' }}">
                                    <i class="fa-solid {{ $card['details_ready'] ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                    <span>Detail event {{ $card['details_ready'] ? 'tersedia' : 'belum lengkap' }}</span>
                                </li>
                                <li class="{{ $card['has_tickets'] ? 'is-ready' : 'is-pending' }}">
                                    <i class="fa-solid {{ $card['has_tickets'] ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                    <span>{{ $card['has_tickets'] ? 'Tiket tersedia' : 'Tiket belum tersedia' }}</span>
                                </li>
                                @php
                                    $approvalReady = $event->approval_status === 'approved';
                                    $approvalText = [
                                        'approved' => 'Approval disetujui',
                                        'pending' => 'Menunggu approval',
                                        'rejected' => 'Approval ditolak',
                                    ][$event->approval_status] ?? 'Status approval tersedia';
                                @endphp
                                <li class="{{ $approvalReady ? 'is-ready' : 'is-pending' }}">
                                    <i class="fa-solid {{ $approvalReady ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                    <span>{{ $approvalText }}</span>
                                </li>
                                @if ($event->seating_type === 'numbered_seat')
                                    <li class="{{ $card['seat_count'] > 0 ? 'is-ready' : 'is-pending' }}">
                                        <i class="fa-solid {{ $card['seat_count'] > 0 ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                        <span>{{ $card['seat_count'] > 0 ? number_format($card['seat_count']) . ' seat terkonfigurasi' : 'Seat belum dikonfigurasi' }}</span>
                                    </li>
                                @else
                                    <li class="is-neutral"><i class="fa-solid fa-minus"></i><span>Seat map tidak diperlukan</span></li>
                                @endif
                            </ul>
                        </article>
                    @endforeach
                </div>
                <p class="eo-health-note">Status approval dan jadwal event ditampilkan terpisah.</p>
            @endif
        </aside>
    </div>
</div>
@endsection
