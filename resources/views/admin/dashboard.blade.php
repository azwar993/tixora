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

            <select>
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
                placeholder="Cari event..."
            >

        </div>

        <select>
            <option>Semua Kategori</option>
            <option>Music</option>
            <option>Sports</option>
            <option>Esports</option>
            <option>Festival</option>
        </select>

        <select>
            <option>Semua Status</option>
            <option>On Going</option>
            <option>Coming Soon</option>
            <option>Past Event</option>
        </select>

    </div>


    <!-- EVENT TABLE -->
    <div class="panel">

        <div class="table-wrapper">

            <table id="eventTable">

                <thead>
                    <tr>
                        <th>EVENT</th>
                        <th>CATEGORY</th>
                        <th>LOCATION</th>
                        <th>DATE</th>
                        <th>PRICE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>

                        <td>
                            <strong>
                                MPL ID Season 18
                            </strong>
                        </td>

                        <td>
                            Esports
                        </td>

                        <td>
                            Jakarta
                        </td>

                        <td>
                            2026
                        </td>

                        <td>
                            Rp85K - Rp150K
                        </td>

                        <td>
                            <span class="status-pill green">
                                ON GOING
                            </span>
                        </td>

                        <td>
                            <div class="action-buttons">

                                <button>
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button>
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <strong>
                                Kahitna 40 Tahun
                            </strong>
                        </td>

                        <td>
                            Music
                        </td>

                        <td>
                            Tangerang
                        </td>

                        <td>
                            5 Sep 2026
                        </td>

                        <td>
                            Rp975K - Rp5M
                        </td>

                        <td>
                            <span class="status-pill orange">
                                COMING
                            </span>
                        </td>

                        <td>
                            <div class="action-buttons">

                                <button>
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button>
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <strong>
                                Jakarta Music Fest
                            </strong>
                        </td>

                        <td>
                            Music
                        </td>

                        <td>
                            Jakarta
                        </td>

                        <td>
                            12 Sep 2026
                        </td>

                        <td>
                            Rp850K - Rp4M
                        </td>

                        <td>
                            <span class="status-pill green">
                                ON GOING
                            </span>
                        </td>

                        <td>
                            <div class="action-buttons">

                                <button>
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button>
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</section>

<!-- CATEGORY SECTION + --> 
 <section
    class="admin-section"
    id="categories"
>
    <div class="section-top">
        <div>
            <span class="topbar-label">
                CATALOG
            </span>

            <h2>
                Categories
            </h2>

            <p>
                Kelola kategori event yang tersedia di platform TIXORA.
            </p>
        </div>

        <button
            class="primary-button"
            onclick="openCategoryForm()"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Kategori
        </button>
    </div>

    <div class="filter-bar">
        <div class="search-admin">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                type="text"
                id="categorySearch"
                placeholder="Cari kategori..."
                oninput="filterCategoryAdmin(this.value)"
            >
        </div>

        <select id="categoryStatusFilter">
            <option value="all">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Non Aktif</option>
        </select>
    </div>

    <div class="category-admin-grid" id="categoryGrid">
        <div class="admin-category-card" data-status="active">
            <div class="category-admin-icon purple">
                <i class="fa-solid fa-music"></i>
            </div>

            <h3>Music</h3>
            <span>18 event aktif</span>

            <div>
                <button type="button" onclick="openCategoryForm('Music')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </button>
                <button type="button" onclick="deleteCategory('Music')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="admin-category-card" data-status="active">
            <div class="category-admin-icon blue">
                <i class="fa-solid fa-futbol"></i>
            </div>

            <h3>Sports</h3>
            <span>9 event aktif</span>

            <div>
                <button type="button" onclick="openCategoryForm('Sports')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </button>
                <button type="button" onclick="deleteCategory('Sports')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="admin-category-card" data-status="active">
            <div class="category-admin-icon green">
                <i class="fa-solid fa-gamepad"></i>
            </div>

            <h3>Esports</h3>
            <span>12 event aktif</span>

            <div>
                <button type="button" onclick="openCategoryForm('Esports')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </button>
                <button type="button" onclick="deleteCategory('Esports')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="admin-category-card" data-status="inactive">
            <div class="category-admin-icon orange">
                <i class="fa-solid fa-star"></i>
            </div>

            <h3>Festival</h3>
            <span>3 event aktif</span>

            <div>
                <button type="button" onclick="openCategoryForm('Festival')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </button>
                <button type="button" onclick="deleteCategory('Festival')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="admin-category-card" data-status="active">
            <div class="category-admin-icon purple">
                <i class="fa-solid fa-masks-theater"></i>
            </div>

            <h3>Theater</h3>
            <span>6 event aktif</span>

            <div>
                <button type="button" onclick="openCategoryForm('Theater')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </button>
                <button type="button" onclick="deleteCategory('Theater')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="admin-category-card" data-status="inactive">
            <div class="category-admin-icon blue">
                <i class="fa-solid fa-people-group"></i>
            </div>

            <h3>Community</h3>
            <span>0 event aktif</span>

            <div>
                <button type="button" onclick="openCategoryForm('Community')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </button>
                <button type="button" onclick="deleteCategory('Community')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

<div class="admin-modal" id="categoryFormModal">
    <div
        class="admin-modal-overlay"
        onclick="closeAdminModal('categoryFormModal')"
    ></div>

    <div class="admin-modal-box">
        <button
            class="modal-close"
            onclick="closeAdminModal('categoryFormModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">
            <span>
                CATEGORY MANAGEMENT
            </span>

            <h2 id="categoryModalTitle">
                Tambah Kategori
            </h2>

            <p>
                Masukkan informasi kategori baru.
            </p>
        </div>

        <form class="admin-form" onsubmit="saveCategory(event)">
            <input type="hidden" id="categoryEditingId" value="">
            <label>
                Nama Kategori
            </label>

            <input
                id="categoryName"
                type="text"
                placeholder="Contoh: Workshop"
                required
            >

            <label>
                Status
            </label>

            <select id="categoryStatus" required>
                <option value="active">Aktif</option>
                <option value="inactive">Non Aktif</option>
            </select>

            <label>
                Deskripsi
            </label>

            <textarea
                id="categoryDescription"
                rows="4"
                placeholder="Deskripsi kategori..."
            ></textarea>

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
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
<!-- EVENT FORM MODAL -->
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
                Masukkan informasi event baru.
            </p>

        </div>


        <form
            class="admin-form"
            onsubmit="saveEvent(event)"
        >

            <label>
                Nama Event
            </label>

            <input
                type="text"
                placeholder="Contoh: TIXORA Music Festival"
                required
            >


            <label>
                Kategori
            </label>

            <select required>

                <option value="">
                    Pilih kategori
                </option>

                <option>Music</option>
                <option>Sports</option>
                <option>Esports</option>
                <option>Festival</option>
                <option>Theater</option>

            </select>


            <div class="form-row">

                <div>

                    <label>
                        Lokasi
                    </label>

                    <input
                        type="text"
                        placeholder="Jakarta"
                        required
                    >

                </div>


                <div>

                    <label>
                        Venue
                    </label>

                    <input
                        type="text"
                        placeholder="GBK"
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
                        required
                    >

                </div>


                <div>

                    <label>
                        Status
                    </label>

                    <select required>

                        <option>
                            Coming Soon
                        </option>

                        <option>
                            On Going
                        </option>

                        <option>
                            Past Event
                        </option>

                    </select>

                </div>

            </div>


            <label>
                Deskripsi
            </label>

            <textarea
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
                    Simpan Event
                </button>

            </div>

        </form>

    </div>

</div>
    </main>

</body>
</html>