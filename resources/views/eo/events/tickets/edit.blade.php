@extends('layouts.eo', ['title' => 'Edit Ticket Type'])

@section('content')
<div class="eo-page eo-ticket-form-page">
    <div class="eo-form-page-heading">
        <div><span class="eo-eyebrow">{{ $event->name }}</span><h2>Edit Ticket Type</h2><p>Perbarui informasi Ticket Type sesuai status event.</p></div>
        <a class="eo-button eo-button-secondary" href="{{ route('eo.events.tickets.index', $event) }}">Kembali</a>
    </div>

    @if (session('error'))<div class="eo-form-notice is-error" role="alert">{{ session('error') }}</div>@endif
    @if ($errors->any())<div class="eo-form-notice is-error" role="alert">Periksa kembali data Ticket Type.</div>@endif
    @if ($isApproved)
        <div class="eo-form-notice">Nama dan harga tidak dapat diubah setelah event disetujui. {{ $isNumberedSeat ? 'Kuota kursi dihitung dari konfigurasi seat.' : 'Kuota tidak boleh lebih kecil dari tiket terjual atau reservasi.' }}</div>
    @endif

    <form class="eo-event-form eo-ticket-form" method="POST" action="{{ route('eo.events.tickets.update', [$event, $ticket]) }}">
        @csrf
        @method('PUT')
        <section class="eo-form-section">
            <div class="eo-form-grid">
                <label class="eo-field eo-field-full" for="name">Nama Ticket
                    <input id="name" name="name" type="text" maxlength="255" value="{{ old('name', $ticket->name) }}" @readonly($isApproved) required>
                    @error('name')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field" for="price">Harga
                    <div class="eo-money-input"><span>Rp</span><input id="price" name="price" type="number" min="0" max="9999999999.99" step="0.01" value="{{ old('price', $ticket->price) }}" @readonly($isApproved) required></div>
                    @error('price')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                @if (! $isNumberedSeat)
                    <label class="eo-field" for="quota">Kuota
                        <input id="quota" name="quota" type="number" min="1" max="4294967295" step="1" value="{{ old('quota', $ticket->quota) }}" required>
                        @error('quota')<small class="eo-field-error">{{ $message }}</small>@enderror
                    </label>
                @else
                    <div class="eo-field eo-ticket-quota-readonly"><span>Kuota kursi</span><strong>{{ number_format($configuredSeatsCount) }} kursi terkonfigurasi</strong></div>
                @endif
                <label class="eo-field eo-field-full" for="description">Deskripsi
                    <textarea id="description" name="description" rows="4">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
            </div>

            <div class="eo-ticket-inventory eo-ticket-inventory-readonly">
                <span><small>Terjual</small><strong>{{ number_format($ticket->sold) }}</strong></span>
                <span><small>Reserved</small><strong>{{ number_format($ticket->reserved) }}</strong></span>
                <span><small>Tersedia</small><strong>{{ number_format($isNumberedSeat ? $availableSeatsCount : $ticket->quota - $ticket->sold - $ticket->reserved) }}</strong></span>
                <small>{{ $isNumberedSeat ? 'Kuota dan inventory dihitung sistem dari seat yang dikonfigurasi.' : 'Inventory dikelola sistem dan tidak dapat diedit.' }}</small>
            </div>
        </section>
        <div class="eo-form-actions">
            <a class="eo-button eo-button-secondary" href="{{ route('eo.events.tickets.index', $event) }}">Batal</a>
            <button class="eo-button eo-button-primary" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
