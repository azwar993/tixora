@extends('layouts.admin', ['title' => 'Orders', 'activeMenu' => 'orders'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">TRANSACTION</span>
                <h2>Orders</h2>
                <p>Monitor pesanan ticket TIXORA.</p>
            </div>

        </div>

        <form class="filter-bar orders-filter-bar" method="GET" action="{{ route('admin.orders.index') }}">
            <div class="search-admin">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari order, customer, atau event..."
                >
            </div>

            <select name="status">
                <option value="">Semua Status</option>
                @foreach (['pending' => 'Pending', 'paid' => 'Paid', 'cancelled' => 'Cancelled', 'expired' => 'Expired'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <label class="date-filter">
                <span>Dari</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </label>
            <label class="date-filter">
                <span>Sampai</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </label>

            <button class="primary-button" type="submit">
                <i class="fa-solid fa-filter"></i>
                Filter
            </button>

            <a class="secondary-button" href="{{ route('admin.orders.index') }}">
                Reset
            </a>
        </form>

        <div class="panel orders-table-panel">
            <div class="table-wrapper orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>CUSTOMER</th>
                            <th>EVENT</th>
                            <th>TICKET</th>
                            <th>QUANTITY</th>
                            <th>TOTAL HARGA</th>
                            <th>STATUS</th>
                            <th>TANGGAL ORDER</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $statusClass = match ($order->status) {
                                    'paid' => 'green',
                                    'pending' => 'orange',
                                    'cancelled' => 'red',
                                    'expired' => 'gray',
                                    default => 'gray',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="order-identifier">
                                        {{ $order->order_code }}
                                    </span>
                                </td>
                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td>{{ $order->ticket->event->name ?? '-' }}</td>
                                <td>{{ $order->ticket->name ?? '-' }}</td>
                                <td>{{ number_format($order->quantity, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a
                                            class="action-button"
                                            href="{{ route('admin.orders.show', $order) }}"
                                            title="Detail Order"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="orders-empty-state">
                                        <div class="orders-empty-icon">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                        <h3>Belum Ada Pesanan</h3>
                                        <p>
                                            Transaksi akan muncul setelah user membeli ticket.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $orders->links() }}
    </section>
@endsection
