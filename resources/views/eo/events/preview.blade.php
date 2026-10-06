@extends('layouts.eo', ['title' => 'Preview Event'])

@section('content')
<div class="eo-page eo-event-preview-page">
    <div class="eo-form-page-heading">
        <div>
            <span class="eo-eyebrow">PREVIEW EVENT</span>
            <h2>{{ $event->name }}</h2>
            <p>Preview ini hanya dapat dilihat oleh pemilik event dan belum tampil untuk publik.</p>
        </div>
        <a class="eo-button eo-button-secondary" href="{{ route('eo.events.edit', $event) }}">Edit Event</a>
    </div>

    @if ($event->approval_status === 'rejected' && filled($event->rejection_reason))
        <div class="eo-form-notice is-rejected"><strong>Catatan Admin</strong><p>{{ $event->rejection_reason }}</p></div>
    @endif

    <article class="eo-preview-card">
        <div class="eo-preview-cover">
            <span class="eo-cover-fallback"><i class="fa-regular fa-image"></i></span>
            @if ($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" alt="Cover {{ $event->name }}" onerror="this.hidden=true">
            @endif
        </div>
        <div class="eo-preview-content">
            <div class="eo-event-badges">
                <span class="eo-approval-badge is-{{ $event->approval_status }}">{{ strtoupper($event->approval_status) }}</span>
                <span class="eo-schedule-badge">{{ strtoupper(str_replace('_', ' ', $event->status)) }}</span>
            </div>
            <span class="eo-event-category">{{ $event->category }}</span>
            <h3>{{ $event->name }}</h3>
            <p class="eo-preview-meta"><i class="fa-regular fa-calendar"></i>{{ $event->event_date?->format('d M Y') }}</p>
            <p class="eo-preview-meta"><i class="fa-solid fa-location-dot"></i>{{ $event->venue }} · {{ $event->location }}</p>
            <p class="eo-preview-description">{{ $event->description ?: 'Belum ada deskripsi event.' }}</p>
            <p class="eo-preview-seating"><strong>Seating Type:</strong> {{ $event->seating_type === 'numbered_seat' ? 'Numbered Seat' : 'General Admission' }}</p>
        </div>
    </article>

    <div class="eo-form-actions eo-preview-actions">
        <a class="eo-button eo-button-secondary" href="{{ route('eo.events.edit', $event) }}">Kembali Edit</a>
        <form method="POST" action="{{ route('eo.events.submit', $event) }}">
            @csrf
            <button class="eo-button eo-button-primary" type="submit">{{ $event->approval_status === 'rejected' ? 'Ajukan Ulang' : 'Submit for Review' }}</button>
        </form>
    </div>
</div>
@endsection
