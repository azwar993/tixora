@extends('layouts.admin', ['title' => 'Payment Detail', 'activeMenu' => 'payments'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">PAYMENT DETAIL</span>
                <h2 class="order-page-identifier">{{ $payment->payment_code }}</h2>
                <p>Informasi payment yang tersimpan di database.</p>
            </div>

            <a class="secondary-button" href="{{ route('admin.payments.index') }}">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Payments
            </a>
        </div>

        @php
            $statusClass = match ($payment->status) {
                'paid' => 'green',
                'pending' => 'orange',
                'failed', 'cancelled' => 'red',
                default => 'gray',
            };
        @endphp

        <div class="panel">
            <div class="panel-header">
                <div>
                    <span>PAYMENT INFORMATION</span>
                    <h3>{{ $payment->payment_code }}</h3>
                </div>
                <span class="status-pill {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
            </div>

            <div class="orders-detail-grid">
                <div>
                    <small>Payment Code</small>
                    <strong>{{ $payment->payment_code }}</strong>
                    <span>Database ID: {{ $payment->id }}</span>
                </div>
                <div>
                    <small>Transaction ID</small>
                    <strong>{{ $payment->transaction_id ?: '-' }}</strong>
                    <span>Status: {{ ucfirst($payment->status) }}</span>
                </div>
                <div>
                    <small>Payment Method</small>
                    <strong>{{ $payment->payment_method ?: '-' }}</strong>
                    <span>Amount: Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div>
                    <small>Paid At</small>
                    <strong>{{ $payment->paid_at?->format('d M Y H:i') ?: '-' }}</strong>
                    <span>Created: {{ $payment->created_at?->format('d M Y H:i') ?: '-' }}</span>
                </div>
                <div>
                    <small>Order</small>
                    <strong>{{ $payment->order?->order_code ?: 'Tidak tersedia' }}</strong>
                    <span>Quantity: {{ $payment->order?->quantity ?? '-' }}</span>
                </div>
                <div>
                    <small>User</small>
                    <strong>{{ $payment->order?->user?->name ?: 'Tidak tersedia' }}</strong>
                    <span>{{ $payment->order?->user?->email ?: 'Order tidak terkait.' }}</span>
                </div>
                <div>
                    <small>Event</small>
                    <strong>{{ $payment->order?->ticket?->event?->name ?: 'Tidak tersedia' }}</strong>
                    <span>{{ $payment->order?->ticket?->event?->venue ?: 'Order tidak terkait.' }}</span>
                </div>
                <div>
                    <small>Ticket</small>
                    <strong>{{ $payment->order?->ticket?->name ?: 'Tidak tersedia' }}</strong>
                    <span>{{ $payment->order ? 'Ticket terkait dengan order.' : 'Order tidak terkait.' }}</span>
                </div>
            </div>
        </div>
    </section>
@endsection
