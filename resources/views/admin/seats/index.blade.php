@extends('layouts.admin', ['title' => 'Seat Management', 'activeMenu' => 'seats'])

@section('content')
<section class="admin-section active seats-page">
    <div class="section-top">
        <div>
            <span class="topbar-label">VENUE</span>
            <h2>Seat Management</h2>
            <p>Konfigurasi dan monitoring kursi berdasarkan event.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="admin-alert success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="admin-alert error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="admin-alert error">{{ $errors->first() }}</div>
    @endif

    <div class="panel seat-event-selector">
        <div class="panel-header">
            <div>
                <span>EVENT</span>
                <h3>Pilih event untuk melihat konfigurasi kursi</h3>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.seats.index') }}" class="seat-selector-form">
            <label for="event_id">Event</label>
            <select id="event_id" name="event_id" onchange="this.form.submit()">
                <option value="">Pilih Event</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" @selected($selectedEvent?->id === $event->id)>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($selectedEvent)
        <div class="seat-event-info panel">
            <div>
                <span>EVENT INFORMATION</span>
                <h3>{{ $selectedEvent->name }}</h3>
                <p>{{ $selectedEvent->venue }} · {{ $selectedEvent->event_date->format('d M Y') }}</p>
            </div>
            <form method="POST" action="{{ route('admin.seats.type.update', $selectedEvent) }}" class="seat-type-form">
                @csrf
                @method('PUT')
                <label for="seating_type">Tipe seating</label>
                <select id="seating_type" name="seating_type" onchange="this.form.submit()">
                    <option value="general_admission" @selected($selectedEvent->seating_type === 'general_admission')>General Admission</option>
                    <option value="numbered_seat" @selected($selectedEvent->seating_type === 'numbered_seat')>Numbered Seat</option>
                </select>
            </form>
        </div>

        @if ($selectedEvent->seating_type === 'general_admission')
            <div class="panel seat-empty-state">
                <div class="orders-empty-icon"><i class="fa-solid fa-person-walking"></i></div>
                <h3>General Admission</h3>
                <p>Event ini menggunakan General Admission dan tidak memiliki kursi bernomor.</p>
            </div>
        @else
            <div class="panel seat-event-selector">
                <div class="panel-header">
                    <div>
                        <span>SEAT MANAGEMENT</span>
                        <h3>Tambah dan filter seat</h3>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.seats.index') }}" class="seat-selector-form">
                    <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                    <label for="seat_search">Cari seat</label>
                    <input id="seat_search" name="search" type="search" value="{{ request('search') }}" placeholder="Kode, section, atau baris">

                    <label for="seat_status">Status</label>
                    <select id="seat_status" name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach (['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button class="secondary-button" type="submit">Cari</button>
                </form>
            </div>

            <div class="seat-management-grid">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <span>SEAT LAYOUT</span>
                            <h3>Monitoring Kursi</h3>
                        </div>
                        <div class="seat-legend">
                            <span><i class="seat-legend-dot available"></i>Available</span>
                            <span><i class="seat-legend-dot reserved"></i>Reserved</span>
                            <span><i class="seat-legend-dot sold"></i>Sold</span>
                        </div>
                    </div>
                    @forelse ($seats->groupBy('section') as $section => $sectionSeats)
                        <div class="seat-section">
                            <h4>{{ $section ?: 'GENERAL' }}</h4>
                            @foreach ($sectionSeats->groupBy('row') as $row => $rowSeats)
                                <div class="admin-seat-row">
                                    @foreach ($rowSeats as $seat)
                                        <span class="admin-seat {{ $seat->status }}" title="{{ ucfirst($seat->status) }}">
                                            {{ $seat->seat_code }}
                                        </span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="seat-empty-state compact">
                            <i class="fa-solid fa-chair"></i>
                            <h3>Belum Ada Kursi</h3>
                            <p>Gunakan konfigurasi di samping untuk membuat kursi secara bulk.</p>
                        </div>
                    @endforelse
                </div>

                <div class="panel seat-config-panel">
                    <div class="panel-header">
                        <div>
                            <span>CONFIGURATION</span>
                            <h3>Generate Seats</h3>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.seats.generate') }}" class="seat-generation-form">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                        <label for="section">Section <small>(opsional)</small></label>
                        <input id="section" name="section" type="text" value="{{ old('section') }}" placeholder="VIP">

                        <label for="rows">Baris</label>
                        <input id="rows" name="rows" type="text" value="{{ old('rows', 'A, B') }}" placeholder="A, B, C" required>
                        @error('rows') <small class="form-error">{{ $message }}</small> @enderror

                        <label for="seats_per_row">Jumlah seat per baris</label>
                        <input id="seats_per_row" name="seats_per_row" type="number" min="1" max="10000" value="{{ old('seats_per_row', 8) }}" required>
                        <small>Maksimal 10.000 seat per proses. Total = jumlah baris x seat per baris.</small>
                        @error('seats_per_row') <small class="form-error">{{ $message }}</small> @enderror

                        <button class="primary-button" type="submit">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            Generate Seats
                        </button>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('admin.seats.store') }}" class="seat-generation-form">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                        <h4>Tambah Seat</h4>

                        <label for="seat_code">Kode seat</label>
                        <input id="seat_code" name="seat_code" type="text" value="{{ old('seat_code') }}" placeholder="A01" required>

                        <label for="seat_section">Section <small>(opsional)</small></label>
                        <input id="seat_section" name="section" type="text" value="{{ old('section') }}" placeholder="VIP">

                        <label for="seat_row">Baris <small>(opsional)</small></label>
                        <input id="seat_row" name="row" type="text" value="{{ old('row') }}" placeholder="A">

                        <label for="new_seat_status">Status</label>
                        <select id="new_seat_status" name="status" required>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="sold">Sold</option>
                        </select>

                        <button class="primary-button" type="submit">
                            <i class="fa-solid fa-plus"></i>
                            Tambah Seat
                        </button>
                    </form>
                </div>
            </div>

            <div class="panel seat-table-panel">
                <div class="panel-header">
                    <div>
                        <span>SEAT LIST</span>
                        <h3>Kelola Seat</h3>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>CODE</th>
                                <th>SECTION</th>
                                <th>ROW</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($seats as $seat)
                                <tr>
                                    <td><strong>{{ $seat->seat_code }}</strong></td>
                                    <td>{{ $seat->section ?: '-' }}</td>
                                    <td>{{ $seat->row ?: '-' }}</td>
                                    <td>{{ ucfirst($seat->status) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.seats.update', $seat) }}" class="seat-inline-form">
                                            @csrf
                                            @method('PUT')
                                            <input name="seat_code" value="{{ $seat->seat_code }}" aria-label="Kode seat {{ $seat->seat_code }}" required maxlength="100">
                                            <input name="section" value="{{ $seat->section }}" aria-label="Section {{ $seat->seat_code }}" maxlength="100">
                                            <input name="row" value="{{ $seat->row }}" aria-label="Baris {{ $seat->seat_code }}" maxlength="100">
                                            <select name="status">
                                                @foreach (['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold'] as $value => $label)
                                                    <option value="{{ $value }}" @selected($seat->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" title="Simpan perubahan seat"><i class="fa-solid fa-check"></i></button>
                                        </form>

                                        @if ($seat->status === 'available')
                                            <form method="POST" action="{{ route('admin.seats.destroy', $seat) }}" onsubmit="return confirm('Hapus seat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus seat"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        @else
                                            <span title="Seat reserved atau sold tidak dapat dihapus"><i class="fa-solid fa-lock"></i></span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">Belum ada seat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="seat-summary panel">
                <div><strong>{{ $seatSummary->get('available', 0) }}</strong><span>Available</span></div>
                <div><strong>{{ $seatSummary->get('reserved', 0) }}</strong><span>Reserved</span></div>
                <div><strong>{{ $seatSummary->get('sold', 0) }}</strong><span>Sold</span></div>
                <div><strong>{{ $seatSummary->sum() }}</strong><span>Total Seat</span></div>
            </div>
        @endif
    @elseif ($events->isEmpty())
        <div class="panel seat-empty-state">
            <div class="orders-empty-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
            <h3>Belum Ada Event</h3>
            <p>Tambahkan event terlebih dahulu untuk mengatur konfigurasi kursi.</p>
        </div>
    @endif
</section>
@endsection
