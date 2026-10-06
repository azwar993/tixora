<x-app-layout>
    <x-slot name="title">Dashboard Pembeli | TIXORA</x-slot>

    <div class="buyer-page">
        <div class="buyer-page-inner">
            <section class="buyer-welcome">
                <div>
                    <span class="buyer-eyebrow">AREA PEMBELI</span>
                    <h1>Halo, {{ auth()->user()->name }}</h1>
                    <p>Semua tiket dan aktivitasmu ada di sini.</p>
                </div>
                <a class="buyer-discover-link" href="{{ route('home') }}#events">
                    <i class="fa-solid fa-compass"></i> Jelajahi Event
                </a>
            </section>

            <section class="buyer-stats" aria-label="Ringkasan akun pembeli">
                <article class="buyer-stat-card">
                    <span class="buyer-stat-icon is-purple"><i class="fa-solid fa-ticket"></i></span>
                    <div><small>TIKET AKTIF</small><strong>{{ number_format($activeTicketCount) }}</strong></div>
                </article>
                <article class="buyer-stat-card">
                    <span class="buyer-stat-icon is-blue"><i class="fa-solid fa-receipt"></i></span>
                    <div><small>PESANAN</small><strong>{{ number_format($orderCount) }}</strong></div>
                </article>
                <article class="buyer-stat-card">
                    <span class="buyer-stat-icon is-green"><i class="fa-regular fa-calendar"></i></span>
                    <div><small>EVENT MENDATANG</small><strong>{{ number_format($upcomingEventCount) }}</strong></div>
                </article>
            </section>

            <section class="buyer-panel" id="tickets">
                <div class="buyer-panel-heading">
                    <div><span class="buyer-eyebrow">SIAP UNTUK HADIR</span><h2>Tiket Mendatang</h2></div>
                    <span class="buyer-panel-count">{{ number_format($upcomingTickets->total()) }} tiket</span>
                </div>

                @if ($upcomingTickets->isEmpty())
                    <div class="buyer-empty-state">
                        <span><i class="fa-solid fa-ticket"></i></span>
                        <h3>Belum ada tiket mendatang</h3>
                        <p>Tiket untuk event mendatang akan tampil di sini setelah pembayaran berhasil.</p>
                        <a href="{{ route('home') }}#events">Temukan event <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                @else
                    <div class="buyer-ticket-list">
                        @foreach ($upcomingTickets as $ticketInstance)
                            @php
                                $event = $ticketInstance->ticket?->event;
                                $seat = $ticketInstance->seat;
                            @endphp
                            @if ($event)
                                <article class="buyer-ticket-card">
                                    <span class="buyer-ticket-icon"><i class="fa-solid fa-ticket"></i></span>
                                    <div class="buyer-ticket-copy">
                                        <h3>{{ $event->name }}</h3>
                                        <p>{{ $ticketInstance->ticket?->name ?? 'Tiket event' }}
                                            @if ($seat) <span>·</span> {{ collect([$seat->section, $seat->row, $seat->seat_code])->filter()->implode(' ') }} @endif
                                        </p>
                                        <small><i class="fa-regular fa-calendar"></i> {{ $event->event_date?->format('d M Y') }} <span>·</span> {{ collect([$event->location, $event->venue])->filter()->implode(' · ') }}</small>
                                    </div>
                                    <span class="buyer-ticket-status">AKTIF</span>
                                </article>
                            @endif
                        @endforeach
                    </div>
                    @if ($upcomingTickets->hasPages())
                        <div class="buyer-pagination">{{ $upcomingTickets->links() }}</div>
                    @endif
                @endif
            </section>

            <section class="buyer-panel buyer-orders-panel" id="orders">
                <div class="buyer-panel-heading">
                    <div><span class="buyer-eyebrow">AKTIVITAS TERBARU</span><h2>Pesanan Terbaru</h2></div>
                    <span class="buyer-panel-count">{{ number_format($orderCount) }} pesanan</span>
                </div>
                @if ($recentOrders->isEmpty())
                    <div class="buyer-inline-empty">Pesananmu akan muncul di sini setelah kamu membeli tiket.</div>
                @else
                    <div class="buyer-order-list">
                        @foreach ($recentOrders as $order)
                            @php
                                $orderStatus = [
                                    'paid' => 'Dibayar',
                                    'pending' => 'Menunggu pembayaran',
                                    'cancelled' => 'Dibatalkan',
                                    'expired' => 'Kedaluwarsa',
                                ][$order->status] ?? ucfirst((string) $order->status);
                            @endphp
                            <article class="buyer-order-row">
                                <span class="buyer-order-icon"><i class="fa-solid fa-receipt"></i></span>
                                <div class="buyer-order-copy">
                                    <strong>{{ $order->ticket?->event?->name ?? $order->ticket?->name ?? 'Pesanan tiket' }}</strong>
                                    <small>{{ $order->order_code }} <span>·</span> {{ $order->created_at?->format('d M Y, H:i') }}</small>
                                </div>
                                <div class="buyer-order-total">
                                    <strong>Rp{{ number_format((float) $order->total_price, 0, ',', '.') }}</strong>
                                    <small>{{ $orderStatus }}</small>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            @if (auth()->user()->role === 'eo')
                <section class="buyer-creator-callout">
                    <div><span class="buyer-eyebrow">EVENT CREATOR</span><h2>Kamu sudah aktif sebagai Event Creator.</h2></div>
                    <a href="{{ route('eo.dashboard') }}">Masuk ke Creator Dashboard <i class="fa-solid fa-arrow-right"></i></a>
                </section>
            @elseif (auth()->user()->role === 'user')
                <section class="buyer-creator-callout">
                    <div><span class="buyer-eyebrow">EVENT CREATOR</span><h2>Punya event sendiri?</h2><p>Kelola event kamu sebagai Event Creator di TIXORA.</p></div>
                    <form method="POST" action="{{ route('creator.activate') }}" onsubmit="return confirm('Mulai jadi Event Creator? Setelah diaktifkan, akun ini dapat membuat dan mengelola event sebagai Event Creator.');">
                        @csrf
                        <button type="submit">Mulai Jadi Event Creator</button>
                    </form>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
