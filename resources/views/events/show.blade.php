<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">
                    TIXORA
                </p>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Event
                </h2>
            </div>

            <a
                href="{{ route('home') }}"
                class="text-sm font-semibold text-indigo-600 hover:text-indigo-700"
            >
                ← Kembali ke Event
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-100 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- EVENT HEADER --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                <div class="grid grid-cols-1 lg:grid-cols-2">

                    {{-- IMAGE --}}
                    <div class="bg-gray-200 min-h-[320px]">

                        @if($event->image)

                            <img
                                src="{{ asset('storage/' . $event->image) }}"
                                alt="{{ $event->name }}"
                                class="w-full h-full min-h-[320px] object-cover"
                            >

                        @else

                            <div class="w-full h-full min-h-[320px] flex items-center justify-center">
                                <span class="text-gray-400 font-semibold">
                                    Tidak ada gambar event
                                </span>
                            </div>

                        @endif

                    </div>


                    {{-- EVENT INFO --}}
                    <div class="p-8">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">
                                    {{ $event->category }}
                                </p>

                                <h1 class="text-3xl font-bold text-gray-900 mt-2">
                                    {{ $event->name }}
                                </h1>

                            </div>


                            @if($event->status === 'on_going')

                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap">
                                    ON GOING
                                </span>

                            @elseif($event->status === 'coming_soon')

                                <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap">
                                    COMING SOON
                                </span>

                            @else

                                <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap">
                                    PAST EVENT
                                </span>

                            @endif

                        </div>


                        <div class="mt-6 space-y-5">

                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">
                                    Tanggal
                                </p>

                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                    {{ $event->event_date?->format('d M Y') ?? '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">
                                    Lokasi
                                </p>

                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                    {{ $event->location }}
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $event->venue }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">
                                    Tipe Seating
                                </p>

                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                    {{ $event->seating_type === 'numbered_seat' ? 'Numbered Seat' : 'General Admission' }}
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- DESCRIPTION --}}
            <div class="bg-white rounded-2xl shadow-sm p-8 mt-8">

                <h2 class="text-xl font-bold text-gray-900">
                    Tentang Event
                </h2>

                <p class="text-gray-600 leading-relaxed mt-4 whitespace-pre-line">
                    {{ $event->description ?: 'Belum ada deskripsi event.' }}
                </p>

            </div>


            {{-- CHECKOUT SECTION --}}
            <div class="mt-8">

                <div class="mb-5">

                    <h2 class="text-2xl font-bold text-gray-900">
                        Pilih Tiket
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Pilih tiket, jumlah, seat bila diperlukan, dan metode pembayaran.
                    </p>

                </div>


                @if($event->status === 'past_event' || ($event->event_date && $event->event_date->isBefore(today())))

                    <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">

                        <div class="text-center">

                            <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                                <svg
                                    class="w-7 h-7 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4m0 4h.01M5.07 19h13.86a2 2 0 001.73-2.6L13.73 4.4a2 2 0 00-3.46 0L3.34 16.4A2 2 0 005.07 19z"
                                    />
                                </svg>
                            </div>

                            <h3 class="text-lg font-bold text-gray-800 mt-4">
                                Event sudah berlalu
                            </h3>

                            <p class="text-sm text-gray-500 mt-2">
                                Pembelian tiket untuk event ini sudah tidak tersedia.
                            </p>

                        </div>

                    </div>

                @elseif($event->tickets->count() === 0)

                    <div class="bg-white rounded-2xl shadow-sm p-10 text-center">

                        <h3 class="text-lg font-bold text-gray-800">
                            Tiket belum tersedia
                        </h3>

                        <p class="text-sm text-gray-500 mt-2">
                            Belum ada ticket type yang dapat dibeli untuk event ini.
                        </p>

                    </div>

                @else

                    <form
                        id="checkoutForm"
                        class="space-y-8"
                    >

                        <input
                            type="hidden"
                            name="_token"
                            value="{{ csrf_token() }}"
                        >

                        {{-- TICKET --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                            @php
                                $defaultTicketId = $event->tickets->first(function ($candidate) use ($event) {
                                    return $event->seating_type === 'numbered_seat'
                                        ? (int) $candidate->available_seat_count > 0
                                        : ($candidate->quota - $candidate->sold - $candidate->reserved) > 0;
                                })?->id;
                            @endphp

                            @foreach($event->tickets as $index => $ticket)

                                @php
                                    $remaining = $event->seating_type === 'numbered_seat'
                                        ? (int) $ticket->available_seat_count
                                        : max(0, $ticket->quota - $ticket->sold - $ticket->reserved);
                                    $available = $remaining > 0;
                                @endphp

                                <label
                                    class="ticket-card relative cursor-pointer"
                                >

                                    <input
                                        type="radio"
                                        name="ticket_id"
                                        value="{{ $ticket->id }}"
                                        class="ticket-radio sr-only"
                                        data-ticket-id="{{ $ticket->id }}"
                                        data-ticket-name="{{ $ticket->name }}"
                                        data-ticket-price="{{ $ticket->price }}"
                                        data-ticket-remaining="{{ $remaining }}"
                                        data-ticket-has-sections="{{ $ticket->has_section_configuration ? '1' : '0' }}"
                                        @disabled(! $available)
                                        {{ $ticket->id === $defaultTicketId ? 'checked' : '' }}
                                    >

                                    <div
                                        class="ticket-card-inner border-2 border-gray-200 bg-white rounded-2xl shadow-sm p-6 h-full transition"
                                    >

                                        <div class="flex items-start justify-between gap-4">

                                            <div>

                                                <h3 class="text-lg font-bold text-gray-900">
                                                    {{ $ticket->name }}
                                                </h3>

                                                @if($ticket->description)

                                                    <p class="text-sm text-gray-500 mt-2">
                                                        {{ $ticket->description }}
                                                    </p>

                                                @endif

                                            </div>

                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 ticket-radio-indicator shrink-0"></div>

                                        </div>


                                        <div class="mt-6">

                                            <p class="text-xs text-gray-400 uppercase tracking-wide">
                                                Harga
                                            </p>

                                            <p class="text-2xl font-bold text-indigo-600 mt-1">
                                                Rp{{ number_format((float) $ticket->price, 0, ',', '.') }}
                                            </p>

                                        </div>


                                        <div class="mt-4">

                                            <p class="text-xs text-gray-400 uppercase tracking-wide">
                                                Ketersediaan
                                            </p>

                                            <p class="text-sm font-semibold mt-1 {{ $available ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($remaining) }} tiket tersedia
                                            </p>

                                        </div>

                                    </div>

                                </label>

                            @endforeach

                        </div>


                        {{-- QUANTITY + PAYMENT --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                            {{-- QUANTITY --}}
                            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                                <h3 class="text-lg font-bold text-gray-900">
                                    Jumlah Tiket
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Pilih jumlah tiket yang ingin dibeli.
                                </p>

                                <div class="mt-5">

                                    <input
                                        type="number"
                                        id="quantity"
                                        name="quantity"
                                        min="1"
                                        value="1"
                                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    >

                                    <p
                                        id="quantityHelp"
                                        class="text-xs text-gray-500 mt-2"
                                    >
                                        -
                                    </p>

                                </div>

                            </div>


                            {{-- PAYMENT METHOD --}}
                            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                                <h3 class="text-lg font-bold text-gray-900">
                                    Pembayaran
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Metode pembayaran akan dipilih langsung di halaman Midtrans.
                                </p>

                                <input
                                    type="hidden"
                                    id="paymentMethod"
                                    name="payment_method"
                                    value="qris"
                                >

                                <div class="mt-5 bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                    <p class="text-sm font-semibold text-indigo-800">
                                        Midtrans Sandbox
                                    </p>

                                    <p class="text-xs text-indigo-700 mt-1">
                                        QRIS, e-wallet, virtual account, kartu, dan metode lain tersedia dari halaman pembayaran Midtrans.
                                    </p>
                                </div>

                            </div>

                        </div>


                        {{-- SEAT --}}
                        @if($event->seating_type === 'numbered_seat')

                            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                                <div class="flex items-center justify-between gap-4">

                                    <div>

                                        <h3 class="text-lg font-bold text-gray-900">
                                            Pilih Seat
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Jumlah seat yang dipilih harus sama dengan jumlah tiket.
                                        </p>

                                    </div>

                                    <div class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                        Dipilih:
                                        <span id="selectedSeatCount">0</span>
                                    </div>

                                </div>


                                @if($event->seats->count() > 0)

                                    <div
                                        id="seatGrid"
                                        class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-3 mt-6"
                                    >

                                        @foreach($event->seats as $seat)

                                            <button
                                                type="button"
                                                class="seat-button border border-gray-200 bg-white hover:bg-indigo-50 hover:border-indigo-400 rounded-xl px-3 py-3 text-sm font-semibold text-gray-700 transition"
                                                data-seat-id="{{ $seat->id }}"
                                                data-seat-code="{{ $seat->seat_code }}"
                                                data-section-id="{{ $seat->section_id }}"
                                                data-ticket-id="{{ $seat->section_id !== null ? ($sectionTicketIds[$seat->section_id] ?? '') : '' }}"
                                            >
                                                {{ $seat->seat_code }}
                                            </button>

                                        @endforeach

                                    </div>

                                    <p id="noTicketSeats" class="hidden mt-6 bg-gray-50 rounded-xl p-6 text-center text-sm text-gray-600">
                                        Belum ada kursi tersedia untuk Ticket Type ini.
                                    </p>

                                @else

                                    <div class="mt-6 bg-gray-50 rounded-xl p-6 text-center">

                                        <p class="text-sm font-semibold text-gray-700">
                                            Belum ada seat available.
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Seat akan muncul setelah tersedia.
                                        </p>

                                    </div>

                                @endif


                                <p
                                    id="seatHelp"
                                    class="text-xs text-gray-500 mt-4"
                                >
                                    -
                                </p>

                            </div>

                        @endif


                        {{-- SUMMARY --}}
                        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                            <h3 class="text-lg font-bold text-gray-900">
                                Ringkasan Pesanan
                            </h3>

                            <div class="mt-5 space-y-4">

                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-gray-500">
                                        Event
                                    </span>

                                    <strong class="text-sm text-gray-800 text-right">
                                        {{ $event->name }}
                                    </strong>
                                </div>


                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-gray-500">
                                        Tiket
                                    </span>

                                    <strong
                                        id="summaryTicket"
                                        class="text-sm text-gray-800 text-right"
                                    >
                                        -
                                    </strong>
                                </div>


                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-gray-500">
                                        Quantity
                                    </span>

                                    <strong
                                        id="summaryQuantity"
                                        class="text-sm text-gray-800"
                                    >
                                        1
                                    </strong>
                                </div>


                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-gray-500">
                                        Harga Tiket
                                    </span>

                                    <strong
                                        id="summarySubtotal"
                                        class="text-sm text-gray-800"
                                    >
                                        Rp0
                                    </strong>
                                </div>


                                <div class="border-t border-gray-100 pt-4 flex items-center justify-between gap-4">

                                    <span class="font-semibold text-gray-900">
                                        Total
                                    </span>

                                    <strong
                                        id="summaryTotal"
                                        class="text-xl font-bold text-indigo-600"
                                    >
                                        Rp0
                                    </strong>

                                </div>

                            </div>


                            <div
                                id="checkoutMessage"
                                class="hidden mt-5 rounded-xl p-4 text-sm"
                            ></div>


                            <button
                                type="submit"
                                id="createOrderButton"
                                class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition"
                            >
                                Buat Pesanan
                            </button>

                        </div>

                    </form>

                    {{-- PAYMENT CONFIRMATION --}}
                    <div
                        id="paymentPanel"
                        class="hidden bg-white rounded-2xl shadow-sm p-6 border border-gray-100 mt-8"
                    >

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide">
                                    PAYMENT
                                </p>

                                <h3 class="text-xl font-bold text-gray-900 mt-1">
                                    Pesanan Menunggu Pembayaran
                                </h3>

                                <p class="text-sm text-gray-500 mt-2">
                                    Order sudah tercatat di database. Lanjutkan pembayaran melalui Midtrans Sandbox.
                                </p>

                            </div>

                            <span
                                id="paymentStatus"
                                class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap"
                            >
                                PENDING
                            </span>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-400">
                                    Order Code
                                </p>

                                <p
                                    id="paymentOrderCode"
                                    class="text-sm font-bold text-gray-800 mt-1"
                                >
                                    -
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-400">
                                    Payment Code
                                </p>

                                <p
                                    id="paymentCode"
                                    class="text-sm font-bold text-gray-800 mt-1"
                                >
                                    -
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-400">
                                    Amount
                                </p>

                                <p
                                    id="paymentAmount"
                                    class="text-sm font-bold text-gray-800 mt-1"
                                >
                                    Rp0
                                </p>
                            </div>

                        </div>


                        <button
                            type="button"
                            id="midtransPaymentButton"
                            class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition"
                            disabled
                        >
                            Bayar dengan Midtrans
                        </button>

                        <p
                            id="midtransPaymentHint"
                            class="text-xs text-gray-500 mt-3"
                        >
                            Snap Token akan tersedia setelah order berhasil dibuat.
                        </p>

                        <button
                            type="button"
                            id="simulatePaymentButton"
                            class="hidden w-full mt-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-xl transition"
                        >
                            Simulasikan Pembayaran (Local Test)
                        </button>


                        <div
                            id="ticketResult"
                            class="hidden mt-8"
                        >

                            <div class="border-t border-gray-100 pt-6">

                                <h4 class="text-lg font-bold text-gray-900">
                                    TicketInstance Berhasil Diterbitkan
                                </h4>

                                <div
                                    id="ticketInstanceList"
                                    class="space-y-3 mt-4"
                                ></div>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>


    <style>
        .ticket-card input:checked + .ticket-card-inner {
            border-color: rgb(79 70 229);
            background: rgb(238 242 255);
        }

        .ticket-card input:checked + .ticket-card-inner .ticket-radio-indicator {
            border-color: rgb(79 70 229);
            background: rgb(79 70 229);
            box-shadow: inset 0 0 0 3px white;
        }

        .seat-button.selected {
            border-color: rgb(79 70 229);
            background: rgb(79 70 229);
            color: white;
        }
    </style>


    @if($event->status !== 'past_event' && !($event->event_date && $event->event_date->isBefore(today())))

        <script
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"
        ></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const checkoutForm = document.getElementById('checkoutForm');
                const checkoutAccessUrl = @json(auth()->check()
                    ? (auth()->user()->hasVerifiedEmail() ? null : route('verification.notice'))
                    : route('login'));
                const quantityInput = document.getElementById('quantity');
                const quantityHelp = document.getElementById('quantityHelp');
                const paymentMethod = document.getElementById('paymentMethod');

                const summaryTicket = document.getElementById('summaryTicket');
                const summaryQuantity = document.getElementById('summaryQuantity');
                const summarySubtotal = document.getElementById('summarySubtotal');
                const summaryTotal = document.getElementById('summaryTotal');

                const checkoutMessage = document.getElementById('checkoutMessage');
                const createOrderButton = document.getElementById('createOrderButton');

                const paymentPanel = document.getElementById('paymentPanel');
                const paymentStatus = document.getElementById('paymentStatus');
                const paymentOrderCode = document.getElementById('paymentOrderCode');
                const paymentCode = document.getElementById('paymentCode');
                const paymentAmount = document.getElementById('paymentAmount');
                const midtransPaymentButton = document.getElementById('midtransPaymentButton');
                const midtransPaymentHint = document.getElementById('midtransPaymentHint');
                const simulatePaymentButton = document.getElementById('simulatePaymentButton');

                const ticketResult = document.getElementById('ticketResult');
                const ticketInstanceList = document.getElementById('ticketInstanceList');

                const selectedSeatCount = document.getElementById('selectedSeatCount');
                const seatHelp = document.getElementById('seatHelp');
                const noTicketSeats = document.getElementById('noTicketSeats');

                const seatButtons = Array.from(
                    document.querySelectorAll('.seat-button')
                );

                let selectedSeats = [];
                let currentOrder = null;
                let currentSnapToken = null;


                function formatRupiah(value) {
                    return 'Rp' + Number(value || 0).toLocaleString('id-ID');
                }


                function getSelectedTicket() {
                    return document.querySelector(
                        'input[name="ticket_id"]:checked'
                    );
                }


                function updateSeatAvailability() {
                    const selectedTicket = getSelectedTicket();
                    let visibleSeatCount = 0;

                    seatButtons.forEach(button => {
                        const sectionId = button.dataset.sectionId || '';
                        const isVisible = Boolean(selectedTicket) && (
                            selectedTicket.dataset.ticketHasSections === '1'
                                ? sectionId !== '' && button.dataset.ticketId === selectedTicket.dataset.ticketId
                                : sectionId === ''
                        );

                        button.hidden = !isVisible;
                        button.disabled = !isVisible;
                        if (isVisible) visibleSeatCount++;
                    });

                    if (noTicketSeats) {
                        noTicketSeats.classList.toggle('hidden', visibleSeatCount > 0);
                    }
                }


                function showMessage(message, type = 'error') {

                    checkoutMessage.classList.remove(
                        'hidden',
                        'bg-red-50',
                        'text-red-700',
                        'bg-green-50',
                        'text-green-700'
                    );

                    if (type === 'success') {
                        checkoutMessage.classList.add(
                            'bg-green-50',
                            'text-green-700'
                        );
                    } else {
                        checkoutMessage.classList.add(
                            'bg-red-50',
                            'text-red-700'
                        );
                    }

                    checkoutMessage.textContent = message;
                }


                function clearMessage() {
                    checkoutMessage.classList.add('hidden');
                    checkoutMessage.textContent = '';
                }


                function resetSeats() {

                    selectedSeats = [];

                    seatButtons.forEach(button => {
                        button.classList.remove('selected');
                    });

                    if (selectedSeatCount) {
                        selectedSeatCount.textContent = '0';
                    }
                }


                function updateSeatState() {

                    if (!selectedSeatCount) {
                        return;
                    }

                    const selectedTicket = getSelectedTicket();
                    const quantity = Number(quantityInput.value || 0);

                    selectedSeatCount.textContent =
                        selectedSeats.length;

                    if (!selectedTicket) {
                        seatHelp.textContent = 'Pilih tiket terlebih dahulu.';
                        return;
                    }

                    if (selectedSeats.length > quantity) {
                        const keep = selectedSeats.slice(0, quantity);

                        selectedSeats = keep;

                        seatButtons.forEach(button => {
                            const seatId = Number(button.dataset.seatId);

                            button.classList.toggle(
                                'selected',
                                selectedSeats.some(
                                    seat => seat.id === seatId
                                )
                            );
                        });
                    }

                    if (selectedSeats.length === quantity) {
                        seatHelp.textContent =
                            'Jumlah seat sudah sesuai dengan quantity.';
                    } else {
                        seatHelp.textContent =
                            `Pilih ${quantity} seat. Saat ini ${selectedSeats.length} dipilih.`;
                    }
                }


                function updateSummary() {

                    const selectedTicket = getSelectedTicket();

                    if (!selectedTicket) {
                        summaryTicket.textContent = '-';
                        summaryQuantity.textContent = '0';
                        summarySubtotal.textContent = 'Rp0';
                        summaryTotal.textContent = 'Rp0';

                        quantityHelp.textContent =
                            'Pilih tiket terlebih dahulu.';

                        return;
                    }

                    const price = Number(
                        selectedTicket.dataset.ticketPrice
                    );

                    const remaining = Number(
                        selectedTicket.dataset.ticketRemaining
                    );

                    let quantity = Number(
                        quantityInput.value || 1
                    );

                    if (quantity < 1) {
                        quantity = 1;
                        quantityInput.value = 1;
                    }

                    if (quantity > remaining) {
                        quantity = remaining;
                        quantityInput.value = remaining;
                    }

                    summaryTicket.textContent =
                        selectedTicket.dataset.ticketName;

                    summaryQuantity.textContent =
                        quantity;

                    const subtotal =
                        price * quantity;

                    summarySubtotal.textContent =
                        formatRupiah(subtotal);

                    summaryTotal.textContent =
                        formatRupiah(subtotal);

                    quantityHelp.textContent =
                        `${remaining.toLocaleString('id-ID')} tiket maksimal untuk ticket type ini.`;

                    updateSeatState();
                }


                seatButtons.forEach(button => {

                    button.addEventListener('click', function () {

                        const seatId = Number(
                            this.dataset.seatId
                        );

                        const seatCode =
                            this.dataset.seatCode;

                        const selectedTicket =
                            getSelectedTicket();

                        if (!selectedTicket) {
                            showMessage(
                                'Pilih ticket terlebih dahulu.'
                            );

                            return;
                        }

                        const quantity =
                            Number(quantityInput.value || 1);

                        const existingIndex =
                            selectedSeats.findIndex(
                                seat => seat.id === seatId
                            );

                        if (existingIndex >= 0) {

                            selectedSeats.splice(
                                existingIndex,
                                1
                            );

                            this.classList.remove(
                                'selected'
                            );

                        } else {

                            if (selectedSeats.length >= quantity) {
                                showMessage(
                                    `Kamu hanya dapat memilih ${quantity} seat.`
                                );

                                return;
                            }

                            selectedSeats.push({
                                id: seatId,
                                code: seatCode,
                            });

                            this.classList.add(
                                'selected'
                            );
                        }

                        clearMessage();
                        updateSeatState();
                    });

                });


                document
                    .querySelectorAll('input[name="ticket_id"]')
                    .forEach(radio => {

                        radio.addEventListener(
                            'change',
                            function () {

                                resetSeats();
                                updateSeatAvailability();
                                clearMessage();
                                updateSummary();
                            }
                        );

                    });


                quantityInput.addEventListener(
                    'input',
                    function () {

                        const selectedTicket =
                            getSelectedTicket();

                        if (!selectedTicket) {
                            this.value = 1;
                            updateSummary();
                            return;
                        }

                        const remaining = Number(
                            selectedTicket.dataset.ticketRemaining
                        );

                        let quantity =
                            Number(this.value || 1);

                        if (quantity < 1) {
                            quantity = 1;
                        }

                        if (quantity > remaining) {
                            quantity = remaining;
                        }

                        this.value = quantity;

                        clearMessage();
                        updateSummary();
                    }
                );


                async function openMidtransPayment() {

                    if (!currentSnapToken) {
                        showMessage(
                            'Snap Token belum tersedia.'
                        );

                        return;
                    }

                    if (!window.snap || typeof window.snap.pay !== 'function') {
                        showMessage(
                            'Midtrans Snap belum berhasil dimuat. Periksa koneksi internet lalu refresh halaman.'
                        );

                        return;
                    }

                    midtransPaymentButton.disabled = true;
                    midtransPaymentButton.textContent =
                        'Membuka Pembayaran...';

                    window.snap.pay(
                        currentSnapToken,
                        {
                            onSuccess: function (result) {
                                console.log(
                                    'Midtrans success:',
                                    result
                                );

                                midtransPaymentButton.textContent =
                                    'Pembayaran Berhasil Diproses';

                                midtransPaymentHint.textContent =
                                    'Pembayaran diterima oleh Snap. Status final akan dikonfirmasi oleh webhook Midtrans ke server TIXORA.';

                                showMessage(
                                    'Pembayaran berhasil dikirim ke Midtrans. Menunggu konfirmasi webhook.',
                                    'success'
                                );
                            },

                            onPending: function (result) {
                                console.log(
                                    'Midtrans pending:',
                                    result
                                );

                                midtransPaymentButton.disabled = false;
                                midtransPaymentButton.textContent =
                                    'Buka Pembayaran Midtrans Lagi';

                                midtransPaymentHint.textContent =
                                    'Pembayaran masih pending. Selesaikan pembayaran di Midtrans atau buka kembali halaman pembayaran.';

                                showMessage(
                                    'Pembayaran masih pending.',
                                    'success'
                                );
                            },

                            onError: function (result) {
                                console.error(
                                    'Midtrans error:',
                                    result
                                );

                                midtransPaymentButton.disabled = false;
                                midtransPaymentButton.textContent =
                                    'Coba Pembayaran Lagi';

                                showMessage(
                                    'Midtrans melaporkan pembayaran gagal.'
                                );
                            },

                            onClose: function () {
                                midtransPaymentButton.disabled = false;
                                midtransPaymentButton.textContent =
                                    'Buka Pembayaran Midtrans';

                                midtransPaymentHint.textContent =
                                    'Popup pembayaran ditutup. Order tetap menunggu pembayaran selama belum expired.';
                            }
                        }
                    );
                }


                midtransPaymentButton.addEventListener(
                    'click',
                    openMidtransPayment
                );


                checkoutForm.addEventListener(
                    'submit',
                    async function (event) {

                        event.preventDefault();

                        if (checkoutAccessUrl) {
                            window.location.assign(checkoutAccessUrl);
                            return;
                        }

                        clearMessage();

                        const selectedTicket =
                            getSelectedTicket();

                        if (!selectedTicket) {
                            showMessage(
                                'Pilih tiket terlebih dahulu.'
                            );

                            return;
                        }

                        const quantity =
                            Number(quantityInput.value || 0);

                        if (quantity < 1) {
                            showMessage(
                                'Quantity harus minimal 1.'
                            );

                            return;
                        }

                        const eventIsNumberedSeat =
                            {{ $event->seating_type === 'numbered_seat' ? 'true' : 'false' }};

                        if (eventIsNumberedSeat) {

                            if (selectedSeats.length !== quantity) {
                                showMessage(
                                    `Pilih tepat ${quantity} seat sebelum checkout.`
                                );

                                return;
                            }
                        }

                        createOrderButton.disabled = true;
                        createOrderButton.textContent =
                            'Membuat Pesanan...';

                        const formData =
                            new FormData(checkoutForm);

                        formData.set(
                            'ticket_id',
                            selectedTicket.value
                        );

                        formData.set(
                            'quantity',
                            String(quantity)
                        );

                        formData.set(
                            'payment_method',
                            paymentMethod.value
                        );

                        formData.delete('seat_ids[]');

                        if (eventIsNumberedSeat) {

                            selectedSeats.forEach(seat => {

                                formData.append(
                                    'seat_ids[]',
                                    String(seat.id)
                                );

                            });

                        }

                        try {

                            const response =
                                await fetch(
                                    '{{ route('checkout.store') }}',
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    }
                                );

                            const data =
                                await response.json();

                            if (! response.ok) {

                                if (data.errors) {

                                    const firstError =
                                        Object.values(data.errors)
                                            .flat()[0];

                                    throw new Error(
                                        firstError || 'Checkout gagal.'
                                    );
                                }

                                throw new Error(
                                    data.message || 'Checkout gagal.'
                                );
                            }

                            currentOrder =
                                data.order;

                            currentSnapToken =
                                data.midtrans?.snap_token || null;

                            if (currentSnapToken) {
                                midtransPaymentButton.disabled = false;
                                midtransPaymentButton.textContent =
                                    'Bayar dengan Midtrans';

                                midtransPaymentHint.textContent =
                                    'Order siap dibayar melalui Midtrans Sandbox.';
                            } else {
                                midtransPaymentButton.disabled = true;
                                midtransPaymentHint.textContent =
                                    'Snap Token tidak diterima dari server.';
                            }

                            paymentPanel.classList.remove(
                                'hidden'
                            );

                            paymentOrderCode.textContent =
                                data.order.order_code;

                            paymentCode.textContent =
                                data.payment?.payment_code || '-';

                            paymentAmount.textContent =
                                formatRupiah(
                                    data.payment?.amount || 0
                                );

                            showMessage(
                                'Order berhasil dibuat. Status sekarang pending.',
                                'success'
                            );

                            createOrderButton.textContent =
                                'Order Berhasil Dibuat';

                            createOrderButton.disabled = true;
                            simulatePaymentButton.classList.add('hidden');

                            paymentPanel.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });

                        } catch (error) {

                            showMessage(
                                error.message ||
                                'Terjadi kesalahan saat membuat order.'
                            );

                            createOrderButton.disabled = false;

                            createOrderButton.textContent =
                                'Buat Pesanan';
                        }

                    }
                );


                simulatePaymentButton.addEventListener(
                    'click',
                    async function () {

                        if (! currentOrder) {
                            showMessage(
                                'Order belum dibuat.'
                            );

                            return;
                        }

                        simulatePaymentButton.disabled =
                            true;

                        simulatePaymentButton.textContent =
                            'Memproses Pembayaran...';

                        try {

                            const formData =
                                new FormData();

                            formData.append(
                                '_token',
                                '{{ csrf_token() }}'
                            );

                            const response =
                                await fetch(
                                    `/checkout/${currentOrder.id}/simulate-payment`,
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    }
                                );

                            const data =
                                await response.json();

                            if (! response.ok) {

                                if (data.errors) {

                                    const firstError =
                                        Object.values(data.errors)
                                            .flat()[0];

                                    throw new Error(
                                        firstError || 'Pembayaran gagal.'
                                    );
                                }

                                throw new Error(
                                    data.message || 'Pembayaran gagal.'
                                );
                            }

                            paymentStatus.textContent =
                                'PAID';

                            paymentStatus.className =
                                'bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap';

                            simulatePaymentButton.textContent =
                                'Pembayaran Berhasil';

                            simulatePaymentButton.disabled =
                                true;

                            ticketResult.classList.remove(
                                'hidden'
                            );

                            ticketInstanceList.innerHTML =
                                '';

                            data.ticket_instances.forEach(
                                function (ticketInstance) {

                                    const card =
                                        document.createElement('div');

                                    card.className =
                                        'bg-gray-50 rounded-xl p-4 border border-gray-100';

                                    card.innerHTML = `
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                            <div>
                                                <p class="text-xs text-gray-400">
                                                    Ticket Code
                                                </p>

                                                <p class="font-bold text-gray-900">
                                                    ${ticketInstance.ticket_code}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Status: ${ticketInstance.status}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-400">
                                                    QR Token
                                                </p>

                                                <p class="text-xs font-mono text-gray-700 break-all">
                                                    ${ticketInstance.qr_token}
                                                </p>
                                            </div>
                                        </div>
                                    `;

                                    ticketInstanceList.appendChild(
                                        card
                                    );

                                }
                            );

                            ticketResult.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });

                        } catch (error) {

                            showMessage(
                                error.message ||
                                'Terjadi kesalahan saat memproses pembayaran.'
                            );

                            simulatePaymentButton.disabled =
                                false;

                            simulatePaymentButton.textContent =
                                'Simulasikan Pembayaran Berhasil';
                        }

                    }
                );


                updateSeatAvailability();
                updateSummary();
            });
        </script>

    @endif

</x-app-layout>
