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

            <button
                class="menu-item"
                data-section="seats"
                onclick="showSection('seats')"
            >
                <i class="fa-solid fa-chair"></i>
                <span>Seat Management</span>
            </button>

            <p class="menu-label">TRANSACTION</p>

            <button
                class="menu-item"
                data-section="orders"
                onclick="showSection('orders')"
            >
                <i class="fa-solid fa-receipt"></i>
                <span>Orders</span>
            </button>

            <button
                class="menu-item"
                data-section="payments"
                onclick="showSection('payments')"
            >
                <i class="fa-solid fa-wallet"></i>
                <span>Payments</span>
            </button>

            <button
                class="menu-item"
                data-section="ticket-validation"
                onclick="showSection('ticket-validation')"
            >
                <i class="fa-solid fa-qrcode"></i>
                <span>QR Validation</span>
            </button>

            <p class="menu-label">MANAGEMENT</p>

            <button
                class="menu-item"
                data-section="users"
                onclick="showSection('users')"
            >
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </button>

            <button
                class="menu-item"
                data-section="articles"
                onclick="showSection('articles')"
            >
                <i class="fa-solid fa-newspaper"></i>
                <span>Articles</span>
            </button>

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

                <button class="notification-button">
                    <i class="fa-regular fa-bell"></i>

                    <span>3</span>
                </button>

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
                        <strong>12</strong>
                        <small>+2 bulan ini</small>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon blue">
                        <i class="fa-solid fa-ticket"></i>
                    </div>

                    <div>
                        <span>TICKETS SOLD</span>
                        <strong>1,245</strong>
                        <small>+18.4%</small>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon green">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                    <div>
                        <span>REVENUE</span>
                        <strong>Rp125,5 Jt</strong>
                        <small>+12.8%</small>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon orange">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>
                        <span>TOTAL USERS</span>
                        <strong>542</strong>
                        <small>+31 user</small>
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

            <div class="recent-order">

                <div class="order-avatar">
                    H
                </div>

                <div>
                    <strong>Haikal</strong>
                    <small>Jakarta Music Fest</small>
                </div>

                <span class="paid-badge">
                    PAID
                </span>

            </div>


            <div class="recent-order">

                <div class="order-avatar">
                    B
                </div>

                <div>
                    <strong>Budi</strong>
                    <small>MPL ID Season 18</small>
                </div>

                <span class="paid-badge">
                    PAID
                </span>

            </div>


            <div class="recent-order">

                <div class="order-avatar">
                    A
                </div>

                <div>
                    <strong>Andi</strong>
                    <small>Kahitna 40 Tahun</small>
                </div>

                <span class="pending-badge">
                    PENDING
                </span>

            </div>


            <div class="recent-order">

                <div class="order-avatar">
                    D
                </div>

                <div>
                    <strong>Dimas</strong>
                    <small>Soundwave Jakarta</small>
                </div>

                <span class="paid-badge">
                    PAID
                </span>

            </div>

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

                <tr>
                    <td>
                        <strong>Jakarta Music Fest</strong>
                    </td>

                    <td>
                        Music
                    </td>

                    <td>
                        12 Sep 2026
                    </td>

                    <td>
                        420
                    </td>

                    <td>
                        Rp850 Jt
                    </td>

                    <td>
                        <span class="status-pill green">
                            ON GOING
                        </span>
                    </td>
                </tr>


                <tr>
                    <td>
                        <strong>MPL ID Season 18</strong>
                    </td>

                    <td>
                        Esports
                    </td>

                    <td>
                        2026
                    </td>

                    <td>
                        315
                    </td>

                    <td>
                        Rp37 Jt
                    </td>

                    <td>
                        <span class="status-pill purple">
                            ON GOING
                        </span>
                    </td>
                </tr>


                <tr>
                    <td>
                        <strong>Kahitna 40 Tahun</strong>
                    </td>

                    <td>
                        Music
                    </td>

                    <td>
                        5 Sep 2026
                    </td>

                    <td>
                        275
                    </td>

                    <td>
                        Rp315 Jt
                    </td>

                    <td>
                        <span class="status-pill orange">
                            COMING
                        </span>
                    </td>
                </tr>

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
                Kelola event yang ditampilkan pada website TIXORA.
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
        <option value="">Semua Status</option>
        <option value="on_going">On Going</option>
        <option value="coming_soon">Coming Soon</option>
        <option value="past_event">Past Event</option>
    </select>

</div>

    <<!-- EVENT TABLE -->
<div class="panel">

    <div class="table-wrapper">

        <table id="eventTable">

            <thead>
                <tr>
                    <th>EVENT</th>
                    <th>CATEGORY</th>
                    <th>LOCATION</th>
                    <th>VENUE</th>
                    <th>DATE</th>
                    <th>PRICE</th>
                    <th>STATUS</th>
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
    >

        <td>
            <strong>
                {{ $event->name }}
            </strong>
        </td>

        <td>
            {{ $event->category }}
        </td>

        <td>
            {{ $event->location }}
        </td>

        <td>
            {{ $event->venue }}
        </td>

        <td>
            {{ $event->event_date->format('d M Y') }}
        </td>

        <td>
            -
        </td>

        <td>
            <span class="status-pill
                @if($event->status === 'on_going')
                    green
                @elseif($event->status === 'coming_soon')
                    orange
                @else
                    purple
                @endif
            ">
                {{ strtoupper(str_replace('_', ' ', $event->status)) }}
            </span>
        </td>

        <td>
            <div class="action-buttons">

                <button
                    type="button"
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

                <form
                    method="POST"
                    action="{{ route('admin.events.destroy', $event->id) }}"
                    onsubmit="return confirm('Yakin ingin menghapus event ini?')"
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>

            </div>
        </td>

    </tr>

@empty

        <tr>
            <td colspan="8" style="text-align: center;">
                Belum ada event.
            </td>
        </tr>

    @endforelse

</tbody>

            </table>

        </div>

    </div>

</section>
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
                Kategori
            </label>

            <select
                id="edit_category"
                name="category"
                required
            >
                <option value="Music">Music</option>
                <option value="Sports">Sports</option>
                <option value="Esports">Esports</option>
                <option value="Festival">Festival</option>
                <option value="Theater">Theater</option>
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
                        <option value="coming_soon">Coming Soon</option>
                        <option value="on_going">On Going</option>
                        <option value="past_event">Past Event</option>
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
                Kategori
            </label>

            <select
                name="category"
                required
            >
                <option value="">Pilih Kategori</option>
                <option value="Music">Music</option>
                <option value="Sports">Sports</option>
                <option value="Esports">Esports</option>
                <option value="Festival">Festival</option>
                <option value="Theater">Theater</option>
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

    </main>

</body>
</html>