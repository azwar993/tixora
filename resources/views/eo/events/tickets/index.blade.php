@extends('layouts.eo', ['title' => 'Ticket Types'])

@section('content')
@php
    $isDraftOrRejected = $event->workflow_status === 'draft' || $event->approval_status === 'rejected';
@endphp
<div class="eo-page eo-ticket-types-page">
    <section class="eo-events-intro">
        <div>
            <span class="eo-eyebrow">{{ $event->name }}</span>
            <h2>Ticket Types</h2>
            <p>Atur jenis tiket, harga, dan kuota untuk event ini.</p>
        </div>
        @if ($canCreate)
            <a class="eo-button eo-button-primary" href="{{ route('eo.events.tickets.create', $event) }}"><i class="fa-solid fa-plus"></i><span>Tambah Ticket Type</span></a>
        @else
            <button class="eo-button eo-button-primary" type="button" disabled><i class="fa-solid fa-lock"></i><span>Tambah Ticket Type</span></button>
        @endif
    </section>

    @if ($event->seating_type === 'numbered_seat')
        <div class="eo-ticket-page-actions">
            <div class="eo-form-notice">Kuota kursi untuk event bernomor dihitung dari kursi yang dikonfigurasi.</div>
            <a class="eo-button eo-button-secondary" href="{{ route('eo.events.seating.index', $event) }}"><i class="fa-solid fa-chair"></i><span>Kelola Seating</span></a>
        </div>
    @endif

    @if (session('success'))<div class="eo-form-notice is-success" role="status">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="eo-form-notice is-error" role="alert">{{ session('error') }}</div>@endif
    @if ($isReviewLocked)
        <div class="eo-form-notice">Ticket sedang dikunci karena event sedang dalam proses review. Ticket Types dapat dilihat tanpa mengubahnya.</div>
    @elseif ($event->approval_status === 'approved')
        <div class="eo-form-notice">Event telah disetujui. Nama dan harga Ticket dikunci; kuota hanya dapat dinaikkan dan deskripsi dapat diperbarui.</div>
    @endif

    @if ($tickets->isEmpty())
        <section class="eo-panel eo-ticket-empty">
            <span class="eo-empty-icon"><i class="fa-solid fa-ticket"></i></span>
            <h3>Belum ada Ticket Type</h3>
            <p>Tambahkan setidaknya satu tipe tiket sebelum mengajukan event.</p>
            @if ($canCreate)
                <a class="eo-button eo-button-primary" href="{{ route('eo.events.tickets.create', $event) }}"><i class="fa-solid fa-plus"></i><span>Tambah Ticket Type</span></a>
            @endif
        </section>
    @else
        <section class="eo-ticket-type-grid" aria-label="Daftar Ticket Types">
            @foreach ($tickets as $ticket)
                @php $available = $event->seating_type === 'numbered_seat'
                    ? ($ticket->available_seats_count ?? 0)
                    : ($ticket->quota - $ticket->sold - $ticket->reserved); @endphp
                <article class="eo-ticket-type-card">
                    <div class="eo-ticket-type-heading">
                        <div><span class="eo-eyebrow">{{ $event->seating_type === 'general_admission' ? 'GENERAL ADMISSION' : 'NUMBERED SEAT' }}</span><h3>{{ $ticket->name }}</h3></div>
                        <strong>Rp{{ number_format((float) $ticket->price, 0, ',', '.') }}</strong>
                    </div>
                    @if ($ticket->description)
                        <p class="eo-ticket-type-description">{{ $ticket->description }}</p>
                    @endif
                    <div class="eo-ticket-inventory">
                        <span><small>{{ $event->seating_type === 'numbered_seat' ? 'Kursi' : 'Kuota' }}</small><strong>{{ number_format($event->seating_type === 'numbered_seat' ? ($ticket->configured_seats_count ?? 0) : $ticket->quota) }}</strong></span>
                        <span><small>Terjual</small><strong>{{ number_format($ticket->sold) }}</strong></span>
                        @if ($event->seating_type === 'general_admission')
                            <span><small>Reserved</small><strong>{{ number_format($ticket->reserved) }}</strong></span>
                            <span><small>Tersedia</small><strong>{{ number_format($available) }}</strong></span>
                        @else
                            <span><small>Tersedia</small><strong>{{ number_format($available) }}</strong></span>
                        @endif
                    </div>
                    <div class="eo-ticket-type-actions">
                        @if (! $isReviewLocked)
                            <a class="eo-button eo-button-secondary" href="{{ route('eo.events.tickets.edit', [$event, $ticket]) }}">Edit</a>
                            @if ($ticket->orders_count === 0)
                                <form method="POST" action="{{ route('eo.events.tickets.destroy', [$event, $ticket]) }}" onsubmit="return confirm('Hapus Ticket Type ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="eo-button eo-button-danger" type="submit">Hapus</button>
                                </form>
                            @else
                                <span class="eo-ticket-order-lock">Terikat transaksi</span>
                            @endif
                        @else
                            <span class="eo-ticket-order-lock">Read-only selama review</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    <div class="eo-ticket-back-link"><a href="{{ route('eo.events.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali ke Event Saya</a></div>
</div>
@endsection
