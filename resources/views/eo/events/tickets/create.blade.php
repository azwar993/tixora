@extends('layouts.eo', ['title' => 'Tambah Ticket Type'])

@section('content')
<div class="eo-page eo-ticket-form-page">
    <div class="eo-form-page-heading">
        <div><span class="eo-eyebrow">{{ $event->name }}</span><h2>Tambah Ticket Type</h2><p>Tambahkan jenis tiket dan atur harganya{{ $isNumberedSeat ? '.' : ' serta kuotanya.' }}</p></div>
        <a class="eo-button eo-button-secondary" href="{{ route('eo.events.tickets.index', $event) }}">Batal</a>
    </div>

    @if ($errors->any())<div class="eo-form-notice is-error" role="alert">Periksa kembali data Ticket Type.</div>@endif

    <form class="eo-event-form eo-ticket-form" method="POST" action="{{ route('eo.events.tickets.store', $event) }}">
        @csrf
        <section class="eo-form-section">
            <div class="eo-form-grid">
                <label class="eo-field eo-field-full" for="name">Nama Ticket
                    <input id="name" name="name" type="text" maxlength="255" value="{{ old('name') }}" required>
                    @error('name')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field" for="price">Harga
                    <div class="eo-money-input"><span>Rp</span><input id="price" name="price" type="number" min="0" max="9999999999.99" step="0.01" value="{{ old('price', '0') }}" required></div>
                    @error('price')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                @if (! $isNumberedSeat)
                    <label class="eo-field" for="quota">Kuota
                        <input id="quota" name="quota" type="number" min="1" max="4294967295" step="1" value="{{ old('quota', '1') }}" required>
                        @error('quota')<small class="eo-field-error">{{ $message }}</small>@enderror
                    </label>
                @else
                    <div class="eo-field eo-ticket-quota-readonly"><span>Kuota kursi</span><strong>Dihitung dari konfigurasi seat</strong></div>
                @endif
                <label class="eo-field eo-field-full" for="description">Deskripsi
                    <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
            <div class="eo-ticket-form-preview"><span>Preview ringkas</span><strong id="ticketPricePreview">Rp0</strong><small><b id="ticketQuotaPreview">{{ $isNumberedSeat ? '0' : '1' }}</b> {{ $isNumberedSeat ? 'kursi terkonfigurasi' : 'tiket' }}</small></div>
        </section>
        <div class="eo-form-actions">
            <a class="eo-button eo-button-secondary" href="{{ route('eo.events.tickets.index', $event) }}">Batal</a>
            <button class="eo-button eo-button-primary" type="submit">Simpan Ticket</button>
        </div>
    </form>
</div>
<script>
    (() => {
        const price = document.getElementById('price');
        const quota = document.getElementById('quota');
        const update = () => {
            document.getElementById('ticketPricePreview').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(price.value || 0));
            if (quota) document.getElementById('ticketQuotaPreview').textContent = quota.value || '0';
        };
        price.addEventListener('input', update);
        if (quota) quota.addEventListener('input', update);
        update();
    })();
</script>
@endsection
