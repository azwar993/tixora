@extends('layouts.admin', ['title' => 'Orders', 'activeMenu' => 'orders'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">ORDER DETAIL</span>
                <h2 class="order-page-identifier">{{ $order->order_code }}</h2>
                <p>Informasi lengkap pesanan ticket.</p>
            </div>

            <a class="secondary-button" href="{{ route('admin.orders.index') }}">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Orders
            </a>
        </div>

        @php
            $statusClass = match ($order->status) {
                'paid' => 'green',
                'pending' => 'orange',
                'cancelled' => 'red',
                'expired' => 'gray',
                default => 'gray',
            };
            $displayPayment = $order->payments->firstWhere('status', 'paid')
                ?? $order->payments->first();
        @endphp

        <div class="panel">
            <div class="panel-header">
                <div>
                    <span>ORDER INFORMATION</span>
                    <h3>{{ $order->order_code }}</h3>
                </div>
                <span class="status-pill {{ $statusClass }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="orders-detail-grid">
                <div>
                    <small>Customer</small>
                    <strong>{{ $order->user->name ?? '-' }}</strong>
                    <span>{{ $order->user->email ?? '-' }}</span>
                </div>
                <div>
                    <small>Event</small>
                    <strong>{{ $order->ticket->event->name ?? '-' }}</strong>
                    <span>{{ $order->ticket->event->venue ?? '-' }}</span>
                </div>
                <div>
                    <small>Ticket</small>
                    <strong>{{ $order->ticket->name ?? '-' }}</strong>
                    <span>Rp{{ number_format($order->ticket->price ?? 0, 0, ',', '.') }} / ticket</span>
                </div>
                <div>
                    <small>Order Date</small>
                    <strong>{{ $order->created_at->format('d M Y H:i') }}</strong>
                    <span>Quantity: {{ number_format($order->quantity, 0, ',', '.') }}</span>
                </div>
                <div>
                    <small>Total Harga</small>
                    <strong>Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>
                    <span>Status order: {{ ucfirst($order->status) }}</span>
                </div>
                <div>
                    <small>Seat</small>
                    <strong>{{ $order->seat_code ?? 'Tidak tersedia' }}</strong>
                    <span>Data seat tidak tersimpan pada order ini.</span>
                </div>
                <div>
                    <small>Payment</small>
                    @if ($displayPayment)
                        <strong>{{ $displayPayment->payment_code }}</strong>
                        <span>{{ $displayPayment->payment_method ?: '-' }} · {{ ucfirst($displayPayment->status) }}</span>
                        <span>Rp{{ number_format($displayPayment->amount, 0, ',', '.') }}</span>
                    @else
                        <strong>Tidak tersedia</strong>
                        <span>Belum ada payment terkait.</span>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
