@extends('layouts.eo', ['title' => 'Seating Configuration'])

@section('content')
<div class="eo-page eo-seating-page">
    <section class="eo-events-intro">
        <div>
            <span class="eo-eyebrow">{{ $event->name }}</span>
            <h2>Seating Configuration</h2>
            <p>Atur section, baris, dan kursi untuk event bernomor.</p>
        </div>
        <a class="eo-button eo-button-secondary" href="{{ route('eo.events.tickets.index', $event) }}"><i class="fa-solid fa-ticket"></i><span>Ticket Types</span></a>
    </section>

    <div class="eo-seating-event-info">
        <span><small>Nama Event</small><strong>{{ $event->name }}</strong></span>
        <span><small>Seating Type</small><strong>Numbered Seat</strong></span>
    </div>

    @if (session('success'))<div class="eo-form-notice is-success" role="status">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="eo-form-notice is-error" role="alert">{{ session('error') }}</div>@endif
    @if ($errors->any())
        <div class="eo-form-notice is-error" role="alert">
            <strong>Periksa kembali konfigurasi seating.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if (! $canManageTopology)
        <div class="eo-form-notice">{{ $isReviewLocked ? 'Konfigurasi seating read-only selama event ditinjau Admin.' : 'Konfigurasi seating read-only karena event sudah memiliki transaksi atau kursi terpakai.' }}</div>
    @endif

    @if ($canManageTopology)
        <section class="eo-panel eo-section-create-panel">
            <div class="eo-panel-heading"><div><span class="eo-eyebrow">TICKET TYPE → SECTION</span><h2>Tambah Section</h2></div></div>
            <form class="eo-section-form" method="POST" action="{{ route('eo.events.sections.store', $event) }}">
                @csrf
                <label class="eo-field" for="section-code">Section Code
                    <input id="section-code" name="code" type="text" maxlength="50" pattern="[A-Za-z0-9_-]+" placeholder="VIP" value="{{ old('code') }}" required>
                </label>
                <label class="eo-field" for="section-name">Section Name
                    <input id="section-name" name="name" type="text" maxlength="100" placeholder="VIP Floor" value="{{ old('name') }}" required>
                </label>
                <label class="eo-field" for="section-ticket">Ticket Type
                    <select id="section-ticket" name="ticket_id" required>
                        <option value="">Pilih Ticket Type</option>
                        @foreach ($tickets as $ticket)
                            <option value="{{ $ticket->id }}" @selected(old('ticket_id') == $ticket->id)>{{ $ticket->name }} — Rp{{ number_format((float) $ticket->price, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="eo-button eo-button-primary" type="submit" @disabled($tickets->isEmpty())><i class="fa-solid fa-plus"></i><span>Tambah Section</span></button>
            </form>
            @if ($tickets->isEmpty())<p class="eo-seating-help">Buat Ticket Type terlebih dahulu sebelum menambah Section.</p>@endif
        </section>
    @endif

    @forelse ($sections as $section)
        <section class="eo-panel eo-section-panel" id="section-{{ $section->id }}">
            <div class="eo-section-heading">
                <div>
                    <span class="eo-eyebrow">{{ $section->code }}</span>
                    <h2>{{ $section->name }}</h2>
                    <p>{{ $section->ticket->name }} <span>•</span> Rp{{ number_format((float) $section->ticket->price, 0, ',', '.') }}</p>
                </div>
                <div class="eo-section-count"><strong>{{ number_format($section->seats_count) }}</strong><small>Kursi</small></div>
            </div>

            @if ($canManageTopology)
                <details class="eo-section-manage">
                    <summary>Kelola Section</summary>
                    <form class="eo-section-form" method="POST" action="{{ route('eo.events.sections.update', [$event, $section]) }}">
                        @csrf @method('PUT')
                        <label class="eo-field">Section Code<input name="code" maxlength="50" pattern="[A-Za-z0-9_-]+" value="{{ $section->code }}" required></label>
                        <label class="eo-field">Section Name<input name="name" maxlength="100" value="{{ $section->name }}" required></label>
                        <label class="eo-field">Ticket Type
                            <select name="ticket_id" required>
                                @foreach ($tickets as $ticket)
                                    <option value="{{ $ticket->id }}" @selected($ticket->id === $section->ticket_id)>{{ $ticket->name }} — Rp{{ number_format((float) $ticket->price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="eo-button eo-button-secondary" type="submit">Simpan Section</button>
                    </form>
                    <form method="POST" action="{{ route('eo.events.sections.destroy', [$event, $section]) }}" onsubmit="return confirm('Hapus section beserta kursi tersedia di dalamnya?')">
                        @csrf @method('DELETE')
                        <button class="eo-button eo-button-danger" type="submit">Hapus Section</button>
                    </form>
                </details>

                <form class="eo-seat-generator" method="POST" action="{{ route('eo.events.sections.seats.generate', [$event, $section]) }}">
                    @csrf
                    <div><strong>Generate Seats</strong><small>Contoh rows: A,B,C · maksimal 10.000 kursi per proses.</small></div>
                    <label class="eo-field" for="rows-{{ $section->id }}">Rows
                        <input id="rows-{{ $section->id }}" name="rows" type="text" maxlength="1000" placeholder="A,B,C" value="{{ old('rows') }}" required>
                    </label>
                    <label class="eo-field" for="seats-per-row-{{ $section->id }}">Seats per row
                        <input id="seats-per-row-{{ $section->id }}" name="seats_per_row" type="number" min="1" max="10000" value="{{ old('seats_per_row', 20) }}" required>
                    </label>
                    <button class="eo-button eo-button-primary" type="submit">Generate Seats</button>
                    <p class="eo-seating-help">Kode kursi harus unik dalam satu event. Contoh: {{ $section->code }}-A01.</p>
                </form>
            @endif

            @if ($section->seats->isEmpty())
                <div class="eo-seating-empty">Belum ada kursi di section ini.</div>
            @else
                <div class="eo-seat-map" aria-label="Peta kursi {{ $section->name }}">
                    @foreach ($section->seats->groupBy(fn ($seat) => $seat->row ?: '-') as $row => $rowSeats)
                        <div class="eo-seat-map-row">
                            <strong class="eo-seat-row-label">{{ $row }}</strong>
                            <div class="eo-seat-map-items">
                                @foreach ($rowSeats as $seat)
                                    <span class="eo-seat-chip is-{{ $seat->status }}" title="{{ $seat->seat_code }} — {{ $seat->status }}">
                                        {{ $seat->seat_code }}
                                        @if ($canManageTopology && $seat->status === 'available' && $seat->order_seats_count === 0 && $seat->ticket_instance_count === 0)
                                            <form method="POST" action="{{ route('eo.events.sections.seats.destroy', [$event, $section, $seat]) }}" onsubmit="return confirm('Hapus kursi {{ $seat->seat_code }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" aria-label="Hapus {{ $seat->seat_code }}">×</button>
                                            </form>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @empty
        <section class="eo-panel eo-seating-empty-state">
            <span class="eo-empty-icon"><i class="fa-solid fa-chair"></i></span>
            <h3>Belum ada Section</h3>
            <p>Tambahkan Ticket Type dan Section untuk mulai mengatur kursi.</p>
        </section>
    @endforelse

    <div class="eo-seat-legend" aria-label="Status kursi">
        <span><i class="is-available"></i>Tersedia</span>
        <span><i class="is-reserved"></i>Reserved</span>
        <span><i class="is-sold"></i>Sold</span>
    </div>
    <div class="eo-ticket-back-link"><a href="{{ route('eo.events.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali ke Event Saya</a></div>
</div>
@endsection
