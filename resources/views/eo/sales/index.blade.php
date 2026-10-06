@extends('layouts.eo', ['title' => 'Penjualan'])

@section('content')
<div class="eo-page eo-sales-page">
    <section class="eo-events-intro eo-sales-intro">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Penjualan</h2>
            <p>Pantau pesanan untuk event yang kamu miliki.</p>
        </div>
    </section>

    <form class="eo-sales-filters" method="GET" action="{{ route('eo.sales.index') }}">
        <label>
            <span>Event</span>
            <select name="event_id">
                <option value="">Semua Event</option>
                @foreach ($ownedEvents as $event)
                    <option value="{{ $event->id }}" @selected((string) $filters['event_id'] === (string) $event->id)>{{ $event->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Status Order</span>
            <select name="status">
                <option value="">Semua Status</option>
                @foreach (['pending', 'paid', 'cancelled', 'expired'] as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Status Pembayaran</span>
            <select name="payment_status">
                <option value="">Semua Status</option>
                @foreach ($paymentStatuses as $status)
                    <option value="{{ $status }}" @selected($filters['payment_status'] === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </label>
        <div class="eo-sales-filter-actions">
            <button class="eo-button eo-button-primary" type="submit"><i class="fa-solid fa-filter"></i><span>Terapkan Filter</span></button>
            <a class="eo-sales-reset" href="{{ route('eo.sales.index') }}">Reset</a>
        </div>
    </form>

    <section class="eo-stats-grid eo-sales-summary" aria-label="Ringkasan penjualan">
        <article class="eo-stat-card">
            <span class="eo-stat-icon blue"><i class="fa-solid fa-receipt"></i></span>
            <div><span>Total Order</span><strong>{{ number_format($summary['orders'], 0, ',', '.') }}</strong></div>
        </article>
        <article class="eo-stat-card">
            <span class="eo-stat-icon green"><i class="fa-solid fa-ticket"></i></span>
            <div><span>Tiket Dipesan</span><strong>{{ number_format($summary['tickets'], 0, ',', '.') }}</strong></div>
        </article>
        <article class="eo-stat-card">
            <span class="eo-stat-icon amber"><i class="fa-solid fa-coins"></i></span>
            <div><span>Nilai Total Order</span><strong>Rp{{ number_format($summary['value'], 0, ',', '.') }}</strong></div>
        </article>
    </section>

    @if ($orders->isEmpty())
        <section class="eo-panel eo-sales-empty">
            <div class="eo-empty-state">
                <span class="eo-empty-icon"><i class="fa-solid fa-receipt"></i></span>
                @if (array_filter($filters))
                    <h3>Tidak ada transaksi yang cocok.</h3>
                    <p>Ubah atau reset filter untuk melihat transaksi event kamu.</p>
                @else
                    <h3>Belum ada transaksi.</h3>
                    <p>Transaksi untuk event yang kamu miliki akan muncul di sini.</p>
                @endif
            </div>
        </section>
    @else
        <section class="eo-panel eo-sales-results" aria-label="Daftar penjualan">
            <div class="eo-sales-table-wrap">
                <table class="eo-sales-table">
                    <thead>
                        <tr>
                            <th>Order Code</th>
                            <th>Event</th>
                            <th>Ticket Type</th>
                            <th>Qty</th>
                            <th>Total Price</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php $paymentStatus = $order->payments->first()?->status; @endphp
                            <tr>
                                <td><strong class="eo-sales-order-code">{{ $order->order_code }}</strong></td>
                                <td>{{ $order->ticket?->event?->name ?? '-' }}</td>
                                <td>{{ $order->ticket?->name ?? '-' }}</td>
                                <td>{{ number_format($order->quantity, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                <td><span class="eo-sales-status is-{{ $order->status }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                                <td>
                                    @if ($paymentStatus)
                                        <span class="eo-sales-status is-{{ $paymentStatus }}">{{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}</span>
                                    @else
                                        <span class="eo-sales-no-payment">Belum ada</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if ($orders->hasPages())
            <div class="eo-pagination">{{ $orders->links() }}</div>
        @endif
    @endif
</div>
@endsection