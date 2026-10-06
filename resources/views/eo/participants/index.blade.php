@extends('layouts.eo', ['title' => 'Peserta'])

@section('content')
<div class="eo-page eo-participants-page">
    <section class="eo-events-intro eo-participants-intro">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Peserta</h2>
            <p>Daftar pemegang tiket yang telah diterbitkan untuk event milikmu.</p>
        </div>
    </section>

    <form class="eo-participants-filters" method="GET" action="{{ route('eo.participants.index') }}">
        <label class="eo-field">
            <span>Event</span>
            <select name="event_id">
                <option value="">Semua Event</option>
                @foreach ($ownedEvents as $event)
                    <option value="{{ $event->id }}" @selected((string) $filters['event_id'] === (string) $event->id)>{{ $event->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="eo-field">
            <span>Status Tiket</span>
            <select name="status">
                <option value="">Semua Status</option>
                <option value="issued" @selected($filters['status'] === 'issued')>Diterbitkan</option>
                <option value="used" @selected($filters['status'] === 'used')>Sudah Check-in</option>
            </select>
        </label>
        <label class="eo-field">
            <span>Cari Peserta</span>
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Nama, email, atau kode tiket">
        </label>
        <div class="eo-participants-filter-actions">
            <button class="eo-button eo-button-primary" type="submit"><i class="fa-solid fa-filter"></i><span>Terapkan Filter</span></button>
            <a class="eo-participants-reset" href="{{ route('eo.participants.index') }}">Reset</a>
        </div>
    </form>

    <section class="eo-stats-grid eo-participants-summary" aria-label="Ringkasan peserta">
        <article class="eo-stat-card">
            <span class="eo-stat-icon purple"><i class="fa-solid fa-users"></i></span>
            <div><span>Total Peserta</span><strong>{{ number_format($summary['participants'], 0, ',', '.') }}</strong></div>
        </article>
        <article class="eo-stat-card">
            <span class="eo-stat-icon blue"><i class="fa-solid fa-ticket"></i></span>
            <div><span>Total Tiket Diterbitkan</span><strong>{{ number_format($summary['tickets'], 0, ',', '.') }}</strong></div>
        </article>
    </section>

    @if ($participants->isEmpty())
        <section class="eo-panel eo-participants-empty">
            <div class="eo-empty-state">
                <span class="eo-empty-icon"><i class="fa-solid fa-users"></i></span>
                @if (array_filter($filters))
                    <h3>Peserta tidak ditemukan</h3>
                    <p>Ubah atau reset filter untuk melihat peserta dari event milikmu.</p>
                @else
                    <h3>Belum ada peserta</h3>
                    <p>Peserta akan muncul di sini setelah tiket diterbitkan.</p>
                @endif
            </div>
        </section>
    @else
        <section class="eo-panel eo-participants-results" aria-label="Daftar peserta">
            <div class="eo-participants-table-wrap">
                <table class="eo-participants-table">
                    <thead>
                        <tr>
                            <th>Peserta</th>
                            <th>Event</th>
                            <th>Jenis Tiket</th>
                            <th>Kode Tiket</th>
                            <th>Kursi</th>
                            <th>Status Tiket</th>
                            <th>Tiket Diterbitkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($participants as $participant)
                            <tr>
                                <td>
                                    <strong class="eo-participant-name">{{ $participant->user?->name ?? '-' }}</strong>
                                    <span class="eo-participant-email">{{ $participant->user?->email ?? '-' }}</span>
                                </td>
                                <td>{{ $participant->ticket?->event?->name ?? '-' }}</td>
                                <td>{{ $participant->ticket?->name ?? '-' }}</td>
                                <td><code class="eo-participant-code">{{ $participant->ticket_code }}</code></td>
                                <td>{{ $participant->seat?->seat_code ?? '-' }}</td>
                                <td><span class="eo-participant-status is-{{ $participant->status }}">{{ $participant->status === 'used' ? 'Sudah Check-in' : 'Diterbitkan' }}</span></td>
                                <td>{{ $participant->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if ($participants->hasPages())
            <div class="eo-pagination">{{ $participants->links() }}</div>
        @endif
    @endif
</div>
@endsection