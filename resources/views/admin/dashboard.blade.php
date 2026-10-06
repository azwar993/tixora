<!DOCTYPE html>
<html lang="id">
<head>
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin | TIXORA</title>

    @vite([
        'resources/css/admin.css',
        'resources/js/admin.js'
    ])

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-logo">
            <a href="#">
                TIX<span>ORA</span>
            </a>

            <small>ADMIN PANEL</small>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">
                <i class="fa-solid fa-user-shield"></i>
            </div>

            <div>
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->email }}</small>
            </div>
        </div>

        <nav class="sidebar-menu">

            <p class="menu-label">MAIN MENU</p>

            <button
                class="menu-item active"
                data-section="dashboard"
                onclick="showSection('dashboard')"
            >
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </button>

            <button
                class="menu-item"
                data-section="events"
                onclick="showSection('events')"
            >
                <i class="fa-solid fa-calendar-days"></i>
                <span>Event Management</span>
            </button>

            <button
                class="menu-item"
                data-section="categories"
                onclick="showSection('categories')"
            >
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>
            </button>

            <button
                class="menu-item"
                data-section="tickets"
                onclick="showSection('tickets')"
            >
                <i class="fa-solid fa-ticket"></i>
                <span>Ticket Management</span>
            </button>

            <a
                class="menu-item"
                data-section="seats"
                href="{{ route('admin.seats.index') }}"
            >
                <i class="fa-solid fa-chair"></i>
                <span>Seat Management</span>
            </a>

            <p class="menu-label">TRANSACTION</p>

            <a
                class="menu-item"
                data-section="orders"
                href="{{ route('admin.orders.index') }}"
            >
                <i class="fa-solid fa-receipt"></i>
                <span>Orders</span>
            </a>

            <button
                class="menu-item"
                data-section="payments"
                onclick="showSection('payments')"
            >
                <i class="fa-solid fa-wallet"></i>
                <span>Payments</span>
            </button>

            <a
                class="menu-item"
                data-section="ticket-validation"
                href="{{ route('admin.qr-validation.index') }}"
            >
                <i class="fa-solid fa-qrcode"></i>
                <span>QR Validation</span>
            </a>

            <p class="menu-label">MANAGEMENT</p>

            <a
                class="menu-item"
                data-section="users"
                href="{{ route('admin.users.index') }}"
            >
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>

            <a
                class="menu-item"
                data-section="articles"
                href="{{ route('admin.articles.index') }}"
            >
                <i class="fa-solid fa-newspaper"></i>
                <span>Articles</span>
            </a>

            <button
                class="menu-item"
                data-section="reports"
                onclick="showSection('reports')"
            >
                <i class="fa-solid fa-file-lines"></i>
                <span>Reports</span>
            </button>

        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="logout-button">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </button>
            </form>
        </div>

    </aside>


    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">

            <div class="topbar-left">

                <button
                    class="sidebar-toggle"
                    onclick="toggleSidebar()"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div>
                    <span class="topbar-label">
                        TIXORA ADMIN
                    </span>

                    <h1 id="pageTitle">
                        Dashboard
                    </h1>
                </div>

            </div>

            <div class="topbar-right">

                <div class="notification-menu">

                <button
                    class="notification-button"
                    type="button"
                    aria-label="Buka notifikasi"
                    aria-expanded="false"
                    aria-controls="notificationPanel"
                    onclick="toggleNotifications()"
                >
                    <i class="fa-regular fa-bell"></i>

                    @if ($adminNotificationCount > 0)
                        <span>{{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}</span>
                    @endif
                </button>

                <div
                    class="notification-panel"
                    id="notificationPanel"
                    role="region"
                    aria-label="Notifikasi"
                    hidden
                >
                    <div class="notification-panel-header">
                        <strong>Notifications</strong>
                        <span>{{ $adminNotificationCount }}</span>
                    </div>

                    <div class="notification-list">
                        @forelse ($adminNotifications as $notification)
                            @php
                                $notificationData = $notification->data ?? [];
                                $notificationUrl = $notificationData['url'] ?? null;
                            @endphp

                            <a
                                class="notification-item"
                                href="{{ $notificationUrl ?: '#' }}"
                                @if (!$notificationUrl) aria-disabled="true" @endif
                            >
                                <span class="notification-item-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                                <span class="notification-item-content">
                                    <strong>{{ $notificationData['title'] ?? 'Notification' }}</strong>
                                    <small>{{ $notificationData['message'] ?? '' }}</small>
                                    @if ($notification->created_at)
                                        <time datetime="{{ $notification->created_at->toISOString() }}">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </time>
                                    @endif
                                </span>
                            </a>
                        @empty
                            <div class="notification-empty">
                                <i class="fa-regular fa-bell-slash"></i>
                                <span>Tidak ada notifikasi baru.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                </div>

                <div class="topbar-admin">

                    <div class="topbar-avatar">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>

                    <div>
                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <small>
                            Administrator
                        </small>
                    </div>

                </div>

            </div>

        </header>


        <!-- DASHBOARD -->
        <section
            class="admin-section active"
            id="dashboard"
        >

            <div class="welcome-banner">

                <div>

                    <span>
                        GOOD DAY, ADMIN
                    </span>

                    <h2>
                        Welcome back to TIXORA.
                    </h2>

                    <p>
                        Pantau event, transaksi dan
                        performa ticketing hari ini.
                    </p>

                </div>

                <div class="banner-icon">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>

            </div>


            <!-- STATISTICS -->
            <div class="stats-grid">

                <div class="stat-card">

                    <div class="stat-icon purple">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>

                    <div>
                        <span>TOTAL EVENT</span>
                        <strong>{{ $totalEvents }}</strong>
                        <small>+{{ $eventsThisMonth }} bulan ini</small>
                        
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon blue">
                        <i class="fa-solid fa-ticket"></i>
                    </div>

                    <div>
                        <span>TICKETS SOLD</span>
                        <strong>{{ number_format($ticketsSold) }}</strong>
                        <small>Total tiket terjual</small>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon green">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                    <div>
                        <span>REVENUE</span>
                        <strong>Rp{{ number_format($revenue, 0, ',', '.') }}</strong>
                        <small>Total pembayaran berhasil</small>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon orange">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>
                        <span>TOTAL USERS</span>
                        <strong>{{ $totalUsers }}</strong>
                        <small>+{{ $usersThisMonth }} user bulan ini</small>
                    </div>

                </div>

            </div>
            <!-- CHART & RECENT ORDERS -->
<div class="dashboard-grid">

    <!-- TICKET SALES -->
    <div class="panel">

        <div class="panel-header">

            <div>
                <span>OVERVIEW</span>

                <h3>
                    Ticket Sales
                </h3>
            </div>

            <select name="category">
                <option>2026</option>
                <option>2025</option>
            </select>

        </div>

        <div class="fake-chart">

            <div class="chart-bars">
                <span style="height:35%;"></span>
                <span style="height:52%;"></span>
                <span style="height:42%;"></span>
                <span style="height:67%;"></span>
                <span style="height:55%;"></span>
                <span style="height:76%;"></span>
                <span style="height:62%;"></span>
                <span style="height:83%;"></span>
                <span style="height:71%;"></span>
                <span style="height:94%;"></span>
                <span style="height:78%;"></span>
                <span style="height:88%;"></span>
            </div>

            <div class="chart-labels">
                <span>Jan</span>
                <span>Feb</span>
                <span>Mar</span>
                <span>Apr</span>
                <span>May</span>
                <span>Jun</span>
                <span>Jul</span>
                <span>Aug</span>
                <span>Sep</span>
                <span>Oct</span>
                <span>Nov</span>
                <span>Dec</span>
            </div>

        </div>

    </div>


    <!-- RECENT ORDERS -->
    <div class="panel">

        <div class="panel-header">

            <div>
                <span>RECENT</span>

                <h3>
                    Orders
                </h3>
            </div>

        </div>

        <div class="recent-order-list">

            @forelse ($recentOrders as $order)
                @php
                    $customerName = optional($order->user)->name ?? '-';
                    $eventName = optional(optional($order->ticket)->event)->name ?? '-';
                    $orderStatus = (string) $order->status;
                    $statusClass = strtolower($orderStatus) === 'paid'
                        ? 'paid-badge'
                        : 'pending-badge';
                @endphp

                <div class="recent-order">

                    <div class="order-avatar">
                        {{ strtoupper(substr($customerName, 0, 1)) }}
                    </div>

                    <div>
                        <strong>{{ $customerName }}</strong>
                        <small>{{ $eventName }}</small>
                    </div>

                    <span class="{{ $statusClass }}">
                        {{ strtoupper($orderStatus) }}
                    </span>

                </div>
            @empty
                <div class="notification-empty">
                    Belum ada order terbaru.
                </div>
            @endforelse

        </div>

    </div>

</div>
<!-- TOP EVENTS -->
<div class="panel">

    <div class="panel-header">

        <div>
            <span>PERFORMANCE</span>

            <h3>
                Top Events
            </h3>
        </div>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>EVENT</th>
                    <th>CATEGORY</th>
                    <th>DATE</th>
                    <th>SOLD</th>
                    <th>REVENUE</th>
                    <th>STATUS</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($topEvents as $topEvent)
                    @php
                        $event = $topEvent['event'];
                        $eventStatusClass = match ($event->status) {
                            'on_going' => 'green',
                            'coming_soon' => 'orange',
                            'past_event' => 'purple',
                            default => 'purple',
                        };
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $event->name }}</strong>
                        </td>

                        <td>
                            {{ $event->category }}
                        </td>

                        <td>
                            {{ optional($event->event_date)->format('d M Y') ?? '-' }}
                        </td>

                        <td>
                            {{ number_format($topEvent['ticketsSold']) }}
                        </td>

                        <td>
                            Rp{{ number_format($topEvent['revenue'], 0, ',', '.') }}
                        </td>

                        <td>
                            <span class="status-pill {{ $eventStatusClass }}">
                                {{ strtoupper(str_replace('_', ' ', $event->status)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999;">
                            Belum ada event.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

        </section>
<!-- EVENT MANAGEMENT -->
<section
    class="admin-section"
    id="events"
>

    <div class="section-top">

        <div>
            <span class="topbar-label">
                CONTENT
            </span>

            <h2>
                Event Management
            </h2>

            <p>
                Review dan kelola event yang diajukan untuk TIXORA.
            </p>
        </div>

        <button
            class="primary-button"
            onclick="openEventForm()"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Event
        </button>

    </div>


    <!-- FILTER -->
    <div class="filter-bar">

        <div class="search-admin">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="eventSearch"
                placeholder="Cari event..."
            >

        </div>

        <select id="eventCategoryFilter">
            <option value="">Semua Kategori</option>
            <option value="Music">Music</option>
            <option value="Sports">Sports</option>
            <option value="Esports">Esports</option>
            <option value="Festival">Festival</option>
            <option value="Theater">Theater</option>
        </select>

        <select id="eventStatusFilter">
            <option value="">Semua Status Event</option>
            <option value="on_going">On Going</option>
            <option value="coming_soon">Coming Soon</option>
            <option value="past_event">Past Event</option>
        </select>

        <select id="eventApprovalFilter">
            <option value="">Semua Approval</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>

    </div>


    <!-- EVENT TABLE -->
    <div class="panel">

        <div class="table-wrapper">

            <table id="eventTable">

                <thead>
                    <tr>
                        <th>EVENT</th>
                        <th>CREATOR / EO</th>
                        <th>CATEGORY</th>
                        <th>LOCATION</th>
                        <th>VENUE</th>
                        <th>DATE</th>
                        <th>TICKETS</th>
                        <th>PRICE</th>
                        <th>STATUS</th>
                        <th>APPROVAL</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($events as $event)

                        <tr
                            data-event-row
                            data-name="{{ strtolower($event->name) }}"
                            data-category="{{ strtolower($event->category) }}"
                            data-location="{{ strtolower($event->location) }}"
                            data-venue="{{ strtolower($event->venue) }}"
                            data-status="{{ $event->status }}"
                            data-approval="{{ $event->approval_status }}"
                        >

                            <!-- EVENT -->
                            <td>
                                <strong>
                                    {{ $event->name }}
                                </strong>
                            </td>

                            <!-- CREATOR / EO -->
                            <td>
                                @if ($event->user)
                                    <strong>{{ $event->user->name }}</strong>
                                    <br>
                                    <small>{{ $event->user->email }}</small>
                                @else
                                    -
                                @endif
                            </td>

                            <!-- CATEGORY -->
                            <td>
                                {{ $event->category }}
                            </td>

                            <!-- LOCATION -->
                            <td>
                                {{ $event->location }}
                            </td>

                            <!-- VENUE -->
                            <td>
                                {{ $event->venue }}
                            </td>

                            <!-- DATE -->
                            <td>
                                {{ $event->event_date->format('d M Y') }}
                            </td>

                            <!-- TICKET INFORMATION -->
                            <td>
                                @php
                                    $ticketTypeCount = $event->tickets->count();
                                    $ticketQuotaTotal = $event->tickets->sum('quota');
                                    $ticketSoldTotal = $event->tickets->sum('sold');
                                @endphp

                                @if ($ticketTypeCount)
                                    <strong>{{ $ticketTypeCount }} {{ $ticketTypeCount === 1 ? 'type' : 'types' }}</strong>
                                    <br>
                                    <small>Quota {{ number_format($ticketQuotaTotal) }} | Sold {{ number_format($ticketSoldTotal) }}</small>
                                @else
                                    -
                                @endif
                            </td>

                            <!-- PRICE -->
                            <td>
                                @if ($ticketTypeCount && $event->tickets->min('price') !== null)
                                    Rp{{ number_format((float) $event->tickets->min('price'), 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- EVENT STATUS -->
                            <td>

                                <span
                                    class="status-pill
                                    @if($event->status === 'on_going')
                                        green
                                    @elseif($event->status === 'coming_soon')
                                        orange
                                    @else
                                        purple
                                    @endif
                                "
                                >
                                    {{ strtoupper(str_replace('_', ' ', $event->status)) }}
                                </span>

                            </td>

                            <!-- APPROVAL STATUS -->
                            <td>

                                @if($event->approval_status === 'pending')

                                    <span class="status-pill orange">
                                        PENDING
                                    </span>

                                @elseif($event->approval_status === 'approved')

                                    <span class="status-pill green">
                                        APPROVED
                                    </span>

                                @else

                                    <span class="status-pill purple">
                                        REJECTED
                                    </span>

                                @endif

                            </td>

                            <!-- ACTION -->
                            <td>

                                <div class="action-buttons">

                                    @if ($event->approval_status === 'approved')
                                        <a
                                            class="action-button"
                                            href="{{ route('events.show', $event) }}"
                                            title="View Event"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif

                                    @if($event->approval_status === 'pending')

                                        <!-- APPROVE -->
                                        <form
                                            method="POST"
                                            action="{{ route('admin.events.approve', $event->id) }}"
                                            onsubmit="return confirm('Approve event ini?')"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                title="Approve Event"
                                            >
                                                <i class="fa-solid fa-check"></i>
                                            </button>

                                        </form>


                                        <!-- REJECT -->
                                        <button
                                            type="button"
                                            title="Reject Event"
                                            onclick="openRejectEventForm(
                                                {{ $event->id }},
                                                {{ Js::from($event->name) }}
                                            )"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>

                                    @endif


                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        title="Edit Event"
                                        onclick="openEditEventForm(
                                            {{ $event->id }},
                                            {{ Js::from($event->name) }},
                                            {{ Js::from($event->category) }},
                                            {{ Js::from($event->location) }},
                                            {{ Js::from($event->venue) }},
                                            {{ Js::from($event->event_date->format('Y-m-d')) }},
                                            {{ Js::from($event->status) }},
                                            {{ Js::from($event->description) }}
                                        )"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>


                                    <!-- DELETE -->
                                    <form
                                        method="POST"
                                        action="{{ route('admin.events.destroy', $event->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus event ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus Event"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="11" style="text-align: center;">
                                Belum ada event.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>
<!-- CATEGORIES -->
<section
    class="admin-section"
    id="categories"
>

    <div class="section-top">

        <div>
            <span class="topbar-label">
                CONTENT
            </span>

            <h2>
                Categories
            </h2>

            <p>
                Kelola kategori event yang tersedia di TIXORA.
            </p>
        </div>

        <button
            class="primary-button"
            type="button"
            onclick="openCategoryForm()"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Category
        </button>

    </div>


    <!-- FILTER -->
    <div class="filter-bar">

        <div class="search-admin">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="categorySearch"
                placeholder="Cari kategori..."
            >

        </div>


        <select id="categoryStatusFilter">
            <option value="">Semua Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

    </div>


    <!-- CATEGORY TABLE -->
    <div class="panel">

        <div class="table-wrapper">

            <table id="categoryTable">

                <thead>
                    <tr>
                        <th>NO</th>
                        <th>CATEGORY</th>
                        <th>DESCRIPTION</th>
                        <th>TOTAL EVENT</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($categories as $category)

                        @php
                            $categoryEventCount = $events
                                ->where('category', $category->name)
                                ->count();
                        @endphp

                        <tr
                            data-category-row
                            data-name="{{ strtolower($category->name) }}"
                            data-description="{{ strtolower($category->description ?? '') }}"
                            data-status="{{ $category->status }}"
                        >

                            <!-- NO -->
                            <td>
                                {{ $loop->iteration }}
                            </td>


                            <!-- CATEGORY -->
                            <td>
                                <strong>
                                    {{ $category->name }}
                                </strong>
                            </td>


                            <!-- DESCRIPTION -->
                            <td>
                                {{ $category->description ?: '-' }}
                            </td>


                            <!-- TOTAL EVENT -->
                            <td>
                                {{ $categoryEventCount }}
                            </td>


                            <!-- STATUS -->
                            <td>

                                @if($category->status === 'active')

                                    <span class="status-pill green">
                                        ACTIVE
                                    </span>

                                @else

                                    <span class="status-pill purple">
                                        INACTIVE
                                    </span>

                                @endif

                            </td>


                            <!-- ACTION -->
                            <td>

                                <div class="action-buttons">

                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        title="Edit Category"
                                        onclick="openEditCategoryForm(
                                            {{ $category->id }},
                                            {{ Js::from($category->name) }},
                                            {{ Js::from($category->description) }},
                                            {{ Js::from($category->status) }}
                                        )"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>


                                    <!-- ACTIVATE / DEACTIVATE -->
                                    <form
                                        method="POST"
                                        action="{{ route('admin.categories.update', $category->id) }}"
                                        onsubmit="return confirm(
                                            '{{ $category->status === 'active'
                                                ? 'Nonaktifkan category ini?'
                                                : 'Aktifkan category ini?' }}'
                                        )"
                                    >

                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="hidden"
                                            name="name"
                                            value="{{ $category->name }}"
                                        >

                                        <input
                                            type="hidden"
                                            name="description"
                                            value="{{ $category->description }}"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="{{ $category->status === 'active'
                                                ? 'inactive'
                                                : 'active' }}"
                                        >

                                        <button
                                            type="submit"
                                            title="{{ $category->status === 'active'
                                                ? 'Deactivate Category'
                                                : 'Activate Category' }}"
                                        >
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>

                                    </form>


                                    <!-- DELETE -->
                                    <form
                                        method="POST"
                                        action="{{ route('admin.categories.destroy', $category->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus category ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus Category"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                style="text-align: center;"
                            >
                                Belum ada category.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>

<!-- TICKET MANAGEMENT -->
<section
    class="admin-section"
    id="tickets"
>

    <div class="section-top">

        <div>
            <span class="topbar-label">
                TRANSACTION
            </span>

            <h2>
                Ticket Management
            </h2>

            <p>
                Pantau seluruh tiket dari event TIXORA.
            </p>
        </div>

        <button
            id="openTicketFormButton"
            class="primary-button"
            type="button"
            onclick="window.openTicketForm()"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Ticket
        </button>

    </div>


    <div class="filter-bar">

        <div class="search-admin">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="ticketSearch"
                placeholder="Cari ticket atau event..."
            >

        </div>

        <select id="ticketEventFilter">
            <option value="">Semua Event</option>

            @foreach ($events as $event)
                <option value="{{ strtolower($event->name) }}">
                    {{ $event->name }}
                </option>
            @endforeach
        </select>

        <select id="ticketStatusFilter">
            <option value="">Semua Status</option>
            <option value="available">Available</option>
            <option value="low-stock">Low Stock</option>
            <option value="sold-out">Sold Out</option>
        </select>

    </div>


    <div class="panel">

        <div class="table-wrapper">

            <table id="ticketTable">

                <thead>
                    <tr>
                        <th>TICKET</th>
                        <th>EVENT</th>
                        <th>PRICE</th>
                        <th>QUOTA</th>
                        <th>SOLD</th>
                        <th>REMAINING</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($tickets as $ticket)

                        @php
                            $remaining = max(0, $ticket->quota - $ticket->sold);

                            if ($remaining <= 0) {
                                $ticketStatus = 'Sold Out';
                                $statusClass = 'red';
                            } elseif ($remaining <= ($ticket->quota * 0.2)) {
                                $ticketStatus = 'Low Stock';
                                $statusClass = 'orange';
                            } else {
                                $ticketStatus = 'Available';
                                $statusClass = 'green';
                            }
                        @endphp

                        <tr
                            data-ticket-row
                            data-ticket-id="{{ $ticket->id }}"
                            data-ticket-name="{{ strtolower($ticket->name) }}"
                            data-event-name="{{ strtolower($ticket->event->name ?? '') }}"
                            data-status="{{ strtolower(str_replace(' ', '-', $ticketStatus)) }}"
                        >

                            <td>
                                <strong>
                                    {{ $ticket->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $ticket->event->name ?? '-' }}
                            </td>

                            <td>
                                Rp{{ number_format($ticket->price, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ number_format($ticket->quota, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ number_format($ticket->sold, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ number_format($remaining, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="status-pill {{ $statusClass }}">
                                    {{ $ticketStatus }}
                                </span>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <button
                                        type="button"
                                        title="Edit Ticket"
                                        onclick="openEditTicketForm(
                                            {{ $ticket->id }},
                                            {{ $ticket->event_id }},
                                            {{ Js::from($ticket->name) }},
                                            {{ Js::from($ticket->price) }},
                                            {{ $ticket->quota }},
                                            {{ Js::from($ticket->description) }}
                                        )"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.tickets.destroy', $ticket->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus ticket ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" title="Hapus Ticket">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" style="text-align: center;">
                                Belum ada ticket.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>

<!-- REPORTS -->
<section class="admin-section" id="reports">
    <div class="section-top">
        <div>
            <span class="topbar-label">ANALYTICS</span>
            <h2>Reports</h2>
            <p>Ringkasan event, transaksi, dan penjualan tiket pada periode terpilih.</p>
        </div>
    </div>

    <form class="filter-bar" method="GET" action="{{ route('admin.dashboard') }}#reports">
        <label for="reportPeriod">Periode laporan</label>
        <select id="reportPeriod" name="report_period" onchange="this.form.submit()">
            <option value="7" @selected($reportPeriod === '7')>7 hari</option>
            <option value="30" @selected($reportPeriod === '30')>30 hari</option>
            <option value="90" @selected($reportPeriod === '90')>90 hari</option>
            <option value="all" @selected($reportPeriod === 'all')>Semua</option>
        </select>
    </form>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon purple"><i class="fa-solid fa-calendar-days"></i></div><div><span>TOTAL EVENTS</span><strong>{{ number_format($reports['summary']['total_events']) }}</strong></div></div>
        <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-users"></i></div><div><span>TOTAL USERS</span><strong>{{ number_format($reports['summary']['total_users']) }}</strong></div></div>
        <div class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-ticket"></i></div><div><span>TICKETS SOLD</span><strong>{{ number_format($reports['summary']['total_tickets_sold']) }}</strong></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div><div><span>PAID REVENUE</span><strong>Rp{{ number_format($reports['summary']['total_paid_revenue'], 0, ',', '.') }}</strong></div></div>
        <div class="stat-card"><div class="stat-icon purple"><i class="fa-solid fa-receipt"></i></div><div><span>TOTAL ORDERS</span><strong>{{ number_format($reports['summary']['total_orders']) }}</strong></div></div>
    </div>

    <div class="panel">
        <div class="panel-header"><div><span>TRANSACTIONS</span><h3>Payment &amp; Order Status</h3></div></div>
        <div class="stats-grid">
            <div class="stat-card"><div><span>PAYMENTS</span>
                @forelse ($reports['summary']['payment_status_counts'] as $status => $total)
                    <p><strong>{{ number_format($total) }}</strong> {{ ucfirst($status) }}</p>
                @empty
                    <p>Belum ada data pembayaran.</p>
                @endforelse
            </div></div>
            <div class="stat-card"><div><span>ORDERS</span>
                @forelse ($reports['summary']['order_status_counts'] as $status => $total)
                    <p><strong>{{ number_format($total) }}</strong> {{ ucfirst($status) }}</p>
                @empty
                    <p>Belum ada data order.</p>
                @endforelse
            </div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><div><span>PERFORMANCE</span><h3>Event Performance</h3></div></div>
        <div class="table-wrapper"><table>
            <thead><tr><th>EVENT</th><th>TICKETS SOLD</th><th>REVENUE</th></tr></thead>
            <tbody>
                @forelse ($reports['event_performance'] as $performance)
                    <tr>
                        <td><strong>{{ $performance['event']->name }}</strong></td>
                        <td>{{ number_format($performance['ticketsSold']) }}</td>
                        <td>Rp{{ number_format($performance['revenue'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align: center; color: #999;">Belum ada performa event pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    <div class="panel">
        <div class="panel-header"><div><span>TREND</span><h3>Revenue, Orders &amp; Tickets Sold</h3></div></div>
        <div class="table-wrapper"><canvas id="reportChart" height="240" style="width: 100%; height: 240px;" aria-label="Grafik revenue, orders, dan tickets sold berdasarkan tanggal"></canvas></div>
    </div>

    <div class="panel">
        <div class="panel-header"><div><span>RECENT</span><h3>Recent Orders</h3></div></div>
        <div class="table-wrapper"><table>
            <thead><tr><th>ORDER</th><th>CUSTOMER</th><th>EVENT</th><th>QUANTITY</th><th>STATUS</th><th>DATE</th></tr></thead>
            <tbody>
                @forelse ($reports['recent_orders'] as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user?->name ?? '-' }}</td>
                        <td>{{ $order->ticket?->event?->name ?? '-' }}</td>
                        <td>{{ number_format($order->quantity) }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ $order->created_at?->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center; color: #999;">Belum ada order pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('reportChart');
    if (!canvas) return;
    const rows = @json($reports['chart']);
    const drawChart = () => {
    const width = canvas.clientWidth;
    if (!width) return;
    const height = canvas.height;
    const ctx = canvas.getContext('2d');
    const padding = { top: 18, right: 16, bottom: 42, left: 58 };
    const series = [
        { key: 'revenue', color: '#7657e8', label: 'Revenue (Rp)' },
        { key: 'orders', color: '#27a6d2', label: 'Orders' },
        { key: 'tickets_sold', color: '#35b779', label: 'Tickets sold' }
    ];
    canvas.width = width * window.devicePixelRatio;
    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    ctx.font = '11px sans-serif';
    ctx.strokeStyle = '#e8eaf0';
    ctx.fillStyle = '#7c8291';
    ctx.beginPath();
    ctx.moveTo(padding.left, padding.top);
    ctx.lineTo(padding.left, height - padding.bottom);
    ctx.lineTo(width - padding.right, height - padding.bottom);
    ctx.stroke();
    if (!rows.length) {
        ctx.fillText('Belum ada data chart pada periode ini.', padding.left + 8, padding.top + 24);
        return;
    }
    const plotWidth = width - padding.left - padding.right;
    const plotHeight = height - padding.top - padding.bottom;
    const maxValues = Object.fromEntries(series.map(item => [item.key, Math.max(1, ...rows.map(row => Number(row[item.key]) || 0))]));
    series.forEach(item => {
        ctx.strokeStyle = item.color;
        ctx.fillStyle = item.color;
        ctx.beginPath();
        rows.forEach((row, index) => {
            const x = padding.left + (rows.length === 1 ? plotWidth / 2 : index * plotWidth / (rows.length - 1));
            const y = padding.top + plotHeight - (Number(row[item.key]) || 0) / maxValues[item.key] * plotHeight;
            if (index === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        });
        ctx.stroke();
        rows.forEach((row, index) => {
            const x = padding.left + (rows.length === 1 ? plotWidth / 2 : index * plotWidth / (rows.length - 1));
            const y = padding.top + plotHeight - (Number(row[item.key]) || 0) / maxValues[item.key] * plotHeight;
            ctx.beginPath(); ctx.arc(x, y, 2.5, 0, Math.PI * 2); ctx.fill();
        });
    });
    const labelStep = Math.max(1, Math.ceil(rows.length / 7));
    rows.forEach((row, index) => {
        if (index % labelStep !== 0 && index !== rows.length - 1) return;
        const x = padding.left + (rows.length === 1 ? plotWidth / 2 : index * plotWidth / (rows.length - 1));
        ctx.fillStyle = '#7c8291'; ctx.fillText(row.date, x - 24, height - 18);
    });
    series.forEach((item, index) => {
        const x = padding.left + index * 135;
        ctx.fillStyle = item.color; ctx.fillRect(x, 4, 9, 9);
        ctx.fillStyle = '#555b68'; ctx.fillText(item.label, x + 14, 13);
    });
    };
    drawChart();
    new ResizeObserver(drawChart).observe(canvas);
});
</script>

<!-- ADD TICKET MODAL -->
<div class="admin-modal" id="ticketFormModal">
    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('ticketFormModal')"
    ></div>

    <div class="admin-modal-box">
        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('ticketFormModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">
            <span>TICKET MANAGEMENT</span>
            <h2>Tambah Ticket</h2>
            <p>Tambahkan ticket untuk event yang tersedia.</p>
        </div>

        <form
            class="admin-form"
            method="POST"
            action="{{ route('admin.tickets.store') }}"
        >
            @csrf

            <label>Event</label>
            <select name="event_id" required>
                <option value="">Pilih Event</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>

            <label>Nama Ticket</label>
            <input type="text" name="name" required>

            <div class="form-row">
                <div>
                    <label>Harga</label>
                    <input type="number" name="price" min="0" step="0.01" required>
                </div>

                <div>
                    <label>Quota</label>
                    <input type="number" name="quota" min="0" step="1" required>
                </div>
            </div>

            <label>Description</label>
            <textarea name="description" rows="4"></textarea>

            <div class="modal-actions">
                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('ticketFormModal')"
                >Batal</button>
                <button type="submit" class="primary-button">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT TICKET MODAL -->
<div class="admin-modal" id="editTicketModal">
    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('editTicketModal')"
    ></div>

    <div class="admin-modal-box">
        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('editTicketModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">
            <span>TICKET MANAGEMENT</span>
            <h2>Edit Ticket</h2>
            <p>Perbarui informasi ticket.</p>
        </div>

        <form class="admin-form" id="editTicketForm" method="POST">
            @csrf
            @method('PUT')

            <label>Event</label>
            <select id="edit_ticket_event_id" name="event_id" required>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>

            <label>Nama Ticket</label>
            <input type="text" id="edit_ticket_name" name="name" required>

            <div class="form-row">
                <div>
                    <label>Harga</label>
                    <input type="number" id="edit_ticket_price" name="price" min="0" step="0.01" required>
                </div>

                <div>
                    <label>Quota</label>
                    <input type="number" id="edit_ticket_quota" name="quota" min="0" step="1" required>
                </div>
            </div>

            <label>Description</label>
            <textarea id="edit_ticket_description" name="description" rows="4"></textarea>

            <div class="modal-actions">
                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('editTicketModal')"
                >Batal</button>
                <button type="submit" class="primary-button">
                    <i class="fa-solid fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EVENT FORM MODAL -->
<!-- EDIT EVENT MODAL -->
<div
    class="admin-modal"
    id="editEventModal"
>

    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('editEventModal')"
    ></div>

    <div class="admin-modal-box">

        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('editEventModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">

            <span>
                EVENT MANAGEMENT
            </span>

            <h2>
                Edit Event
            </h2>

            <p>
                Perbarui informasi event.
            </p>

        </div>

        <form
            class="admin-form"
            id="editEventForm"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <label>
                Nama Event
            </label>

            <input
                type="text"
                id="edit_name"
                name="name"
                required
            >

            <label>
                Poster / Gambar Baru
            </label>

            <input
                type="file"
                id="edit_image"
                name="image"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            >

            <label>
                Kategori
            </label>

            <select
                id="edit_category"
                name="category"
                required
            >
                @foreach ($categories as $category)
                    @if ($category->status === 'active')
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endif
                @endforeach

                @foreach ($events->pluck('category')->filter()->unique() as $existingEventCategory)
                    @php
                        $existingCategoryIsActive = $categories->contains(
                            fn ($category) => $category->status === 'active'
                                && $category->name === $existingEventCategory
                        );
                    @endphp

                    @if (! $existingCategoryIsActive)
                        <option value="{{ $existingEventCategory }}">
                            {{ $existingEventCategory }} (kategori event saat ini)
                        </option>
                    @endif
                @endforeach
            </select>

            <div class="form-row">

                <div>

                    <label>
                        Lokasi
                    </label>

                    <input
                        type="text"
                        id="edit_location"
                        name="location"
                        required
                    >

                </div>

                <div>

                    <label>
                        Venue
                    </label>

                    <input
                        type="text"
                        id="edit_venue"
                        name="venue"
                        required
                    >

                </div>

            </div>

            <div class="form-row">

                <div>

                    <label>
                        Tanggal
                    </label>

                    <input
                        type="date"
                        id="edit_event_date"
                        name="event_date"
                        required
                    >

                </div>

                <div>

                    <label>
                        Status
                    </label>

                    <select
                        id="edit_status"
                        name="status"
                        required
                    >
                        <option value="coming_soon">
                            Coming Soon
                        </option>

                        <option value="on_going">
                            On Going
                        </option>

                        <option value="past_event">
                            Past Event
                        </option>
                    </select>

                </div>

            </div>

            <label>
                Deskripsi
            </label>

            <textarea
                id="edit_description"
                name="description"
                rows="4"
                placeholder="Deskripsi event..."
            ></textarea>

            <div class="modal-actions">

                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('editEventModal')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>


<!-- REJECT EVENT MODAL -->
<div
    class="admin-modal"
    id="rejectEventModal"
>

    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('rejectEventModal')"
    ></div>

    <div class="admin-modal-box">

        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('rejectEventModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">

            <span>
                EVENT APPROVAL
            </span>

            <h2>
                Tolak Event
            </h2>

            <p id="rejectEventName">
                Berikan alasan penolakan event.
            </p>

        </div>

        <form
            class="admin-form"
            id="rejectEventForm"
            method="POST"
        >

            @csrf

            <label>
                Alasan Penolakan
            </label>

            <textarea
                name="rejection_reason"
                rows="5"
                placeholder="Masukkan alasan penolakan event..."
                required
            ></textarea>

            <div class="modal-actions">

                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('rejectEventModal')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="primary-button"
                >
                    <i class="fa-solid fa-xmark"></i>
                    Tolak Event
                </button>

            </div>

        </form>

    </div>
</div>

<!-- ADD EVENT MODAL -->
<div
    class="admin-modal"
    id="eventFormModal"
>

    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('eventFormModal')"
    ></div>

    <div class="admin-modal-box">

        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('eventFormModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">

            <span>
                EVENT MANAGEMENT
            </span>

            <h2>
                Tambah Event
            </h2>

            <p>
                Tambahkan informasi event baru.
            </p>

        </div>

        <form
            class="admin-form"
            method="POST"
            action="{{ route('admin.events.store') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <label>
                Nama Event
            </label>

            <input
                type="text"
                name="name"
                required
            >

            <label>
                Poster / Gambar Event
            </label>

            <input
                type="file"
                name="image"
                accept="image/*"
                required
            >

            <label>
                Kategori
            </label>

            <select
                name="category"
                required
            >
                @if ($categories->contains(fn ($category) => $category->status === 'active'))
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        @if ($category->status === 'active')
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                @else
                    <option value="" disabled selected>Tidak ada kategori aktif</option>
                @endif
            </select>

            <div class="form-row">

                <div>

                    <label>
                        Lokasi
                    </label>

                    <input
                        type="text"
                        name="location"
                        required
                    >

                </div>

                <div>

                    <label>
                        Venue
                    </label>

                    <input
                        type="text"
                        name="venue"
                        required
                    >

                </div>

            </div>

            <div class="form-row">

                <div>

                    <label>
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="event_date"
                        required
                    >

                </div>

                <div>

                    <label>
                        Status
                    </label>

                    <select
                        name="status"
                        required
                    >
                        <option value="coming_soon">
                            Coming Soon
                        </option>

                        <option value="on_going">
                            On Going
                        </option>

                        <option value="past_event">
                            Past Event
                        </option>
                    </select>

                </div>

            </div>

            <label>
                Deskripsi
            </label>

            <textarea
                name="description"
                rows="4"
                placeholder="Deskripsi event..."
            ></textarea>

            <div class="modal-actions">

                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('eventFormModal')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Tambah Event
                </button>

            </div>

        </form>

    </div>

</div>
<!-- ADD CATEGORY MODAL -->
<div
    class="admin-modal"
    id="categoryFormModal"
>

    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('categoryFormModal')"
    ></div>

    <div class="admin-modal-box">

        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('categoryFormModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">

            <span>
                CATEGORY MANAGEMENT
            </span>

            <h2>
                Tambah Category
            </h2>

            <p>
                Tambahkan kategori event baru ke TIXORA.
            </p>

        </div>

        <form
            class="admin-form"
            method="POST"
            action="{{ route('admin.categories.store') }}"
        >

            @csrf

            <label>
                Nama Category
            </label>

            <input
                type="text"
                name="name"
                id="category_name"
                placeholder="Contoh: Music"
                required
            >

            <label>
                Description
            </label>

            <textarea
                name="description"
                id="category_description"
                rows="4"
                placeholder="Deskripsi category..."
            ></textarea>

            <label>
                Status
            </label>

            <select
                name="status"
                id="category_status"
                required
            >
                <option value="active">
                    Active
                </option>

                <option value="inactive">
                    Inactive
                </option>
            </select>

            <div class="modal-actions">

                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('categoryFormModal')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="primary-button"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Category
                </button>

            </div>

        </form>

    </div>
</div>


<!-- EDIT CATEGORY MODAL -->
<div
    class="admin-modal"
    id="editCategoryModal"
>

    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('editCategoryModal')"
    ></div>

    <div class="admin-modal-box">

        <button
            type="button"
            class="modal-close"
            onclick="closeAdminModal('editCategoryModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">

            <span>
                CATEGORY MANAGEMENT
            </span>

            <h2>
                Edit Category
            </h2>

            <p>
                Perbarui informasi category.
            </p>

        </div>

        <form
            class="admin-form"
            id="editCategoryForm"
            method="POST"
        >

            @csrf
            @method('PUT')

            <label>
                Nama Category
            </label>

            <input
                type="text"
                name="name"
                id="edit_category_name"
                required
            >

            <label>
                Description
            </label>

            <textarea
                name="description"
                id="edit_category_description"
                rows="4"
                placeholder="Deskripsi category..."
            ></textarea>

            <label>
                Status
            </label>

            <select
                name="status"
                id="edit_category_status"
                required
            >
                <option value="active">
                    Active
                </option>

                <option value="inactive">
                    Inactive
                </option>
            </select>

            <div class="modal-actions">

                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeAdminModal('editCategoryModal')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="primary-button"
                >
                    <i class="fa-solid fa-save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>

    </main>

</body>
</html>
