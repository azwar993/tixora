@extends('layouts.admin', ['title' => 'Payments', 'activeMenu' => 'payments'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">TRANSACTION</span>
                <h2>Payments</h2>
                <p>Monitor pembayaran yang tersimpan di database.</p>
            </div>
        </div>

        <form class="filter-bar orders-filter-bar" method="GET" action="{{ route('admin.payments.index') }}">
            <div class="search-admin">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari payment code atau transaction ID..."
                >
            </div>

            <select name="status">
                <option value="">Semua Status</option>
                @foreach (['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'cancelled' => 'Cancelled', 'expired' => 'Expired'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <input type="text" name="payment_method" value="{{ request('payment_method') }}" placeholder="Metode payment">

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

            <a class="secondary-button" href="{{ route('admin.payments.index') }}">Reset</a>
        </form>

        <div class="panel orders-table-panel">
            <div class="table-wrapper orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>PAYMENT</th>
                            <th>TRANSACTION ID</th>
                            <th>ORDER</th>
                            <th>USER</th>
                            <th>EVENT</th>
                            <th>TICKET</th>
                            <th>METHOD</th>
                            <th>AMOUNT</th>
                            <th>STATUS</th>
                            <th>PAID AT</th>
                            <th>CREATED AT</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $statusClass = match ($payment->status) {
                                    'paid' => 'green',
                                    'pending' => 'orange',
                                    'failed', 'cancelled' => 'red',
                                    default => 'gray',
                                };
                            @endphp
                            <tr>
                                <td><span class="order-identifier">{{ $payment->payment_code }}</span></td>
                                <td>{{ $payment->transaction_id ?: '-' }}</td>
                                <td>{{ $payment->order?->order_code ?: '-' }}</td>
                                <td>{{ $payment->order?->user?->name ?: '-' }}</td>
                                <td>{{ $payment->order?->ticket?->event?->name ?: '-' }}</td>
                                <td>{{ $payment->order?->ticket?->name ?: '-' }}</td>
                                <td>{{ $payment->payment_method ?: '-' }}</td>
                                <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td><span class="status-pill {{ $statusClass }}">{{ ucfirst($payment->status) }}</span></td>
                                <td>{{ $payment->paid_at?->format('d M Y H:i') ?: '-' }}</td>
                                <td>{{ $payment->created_at?->format('d M Y H:i') ?: '-' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="action-button" href="{{ route('admin.payments.show', $payment) }}" title="Detail Payment">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">
                                    <div class="orders-empty-state">
                                        <div class="orders-empty-icon"><i class="fa-solid fa-wallet"></i></div>
                                        <h3>Belum Ada Payment</h3>
                                        <p>Payment akan muncul setelah transaksi tersimpan di database.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $payments->links() }}
    </section>
@endsection
