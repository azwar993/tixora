@extends('layouts.admin', ['title' => 'QR Validation', 'activeMenu' => 'ticket-validation'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">TICKET ACCESS</span>
                <h2>QR Validation</h2>
                <p>Validasi ticket instance dari database.</p>
            </div>
        </div>

        @if (session('validation_success'))
            <div class="admin-alert success">{{ session('validation_success') }}</div>
        @endif

        @if (session('validation_error'))
            <div class="admin-alert error">{{ session('validation_error') }}</div>
        @endif

        @if ($errors->any())
            <div class="admin-alert error">{{ $errors->first() }}</div>
        @endif

        <div class="panel qr-validation-panel">
            <div class="panel-header">
                <div>
                    <span>VALIDATE TICKET</span>
                    <h3>Masukkan ticket code atau QR token</h3>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.qr-validation.validate') }}" class="qr-validation-form">
                @csrf
                <label for="ticket_code">Ticket code / QR token</label>
                <input id="ticket_code" name="code" value="{{ old('code', $inputCode ?? '') }}" required maxlength="255" autocomplete="off">
                <button class="primary-button" type="submit">
                    <i class="fa-solid fa-qrcode"></i>
                    Validate
                </button>
            </form>
        </div>

        @isset($validationError)
            <div class="panel qr-result invalid">
                <i class="fa-solid fa-circle-xmark"></i>
                <strong>Ticket tidak valid</strong>
                <span>{{ $validationError }}</span>
            </div>
        @endisset

        @isset($ticket)
            @php
                $isPaid = $ticket->order?->payments?->contains('status', 'paid');
                $statusLabel = match ($ticket->status) {
                    'issued' => 'Valid dan belum digunakan',
                    'used' => 'Sudah digunakan',
                    'cancelled' => 'Dibatalkan',
                    default => ucfirst($ticket->status),
                };
            @endphp
            <div class="panel qr-result {{ $isPaid ? 'valid' : 'invalid' }}">
                <div class="panel-header">
                    <div>
                        <span>VALIDATION RESULT</span>
                        <h3>{{ $isPaid ? 'Ticket ditemukan' : 'Ticket tidak valid' }}</h3>
                    </div>
                    <span class="status-pill {{ $ticket->status === 'issued' && $isPaid ? 'green' : 'gray' }}">{{ $statusLabel }}</span>
                </div>

                <div class="orders-detail-grid">
                    <div><small>Ticket Code</small><strong>{{ $ticket->ticket_code }}</strong><span>{{ $ticket->qr_token }}</span></div>
                    <div><small>User</small><strong>{{ $ticket->user?->name ?: '-' }}</strong><span>{{ $ticket->user?->email ?: '-' }}</span></div>
                    <div><small>Event</small><strong>{{ $ticket->ticket?->event?->name ?: '-' }}</strong><span>{{ $ticket->ticket?->event?->venue ?: '-' }}</span></div>
                    <div><small>Ticket Type</small><strong>{{ $ticket->ticket?->name ?: '-' }}</strong><span>Order: {{ $ticket->order?->order_code ?: '-' }}</span></div>
                    <div><small>Seat</small><strong>{{ $ticket->seat?->seat_code ?: 'General Admission' }}</strong><span>{{ $ticket->seat?->section ?: '-' }}</span></div>
                    <div><small>Payment</small><strong>{{ $isPaid ? 'Paid' : 'Unpaid' }}</strong><span>{{ $ticket->order?->payments?->sortByDesc('created_at')->first()?->payment_method ?: '-' }}</span></div>
                    <div><small>Checked In At</small><strong>{{ $ticket->checked_in_at?->format('d M Y H:i') ?: '-' }}</strong><span>By: {{ $ticket->checkedInBy?->name ?: '-' }}</span></div>
                </div>

                @if ($isPaid && $ticket->status === 'issued')
                    <form method="POST" action="{{ route('admin.qr-validation.check-in', $ticket) }}">
                        @csrf
                        <button class="primary-button" type="submit">
                            <i class="fa-solid fa-check"></i>
                            Check-in Ticket
                        </button>
                    </form>
                @elseif ($ticket->status === 'used')
                    <p class="qr-result-message">Ticket ini sudah digunakan dan tidak dapat check-in ulang.</p>
                @elseif ($ticket->status === 'cancelled')
                    <p class="qr-result-message">Ticket ini sudah dibatalkan.</p>
                @else
                    <p class="qr-result-message">Payment belum paid. Ticket tidak dapat digunakan.</p>
                @endif
            </div>
        @endisset
    </section>
@endsection
