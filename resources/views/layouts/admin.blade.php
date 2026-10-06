<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} | TIXORA</title>

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
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <a href="{{ route('admin.dashboard') }}">TIX<span>ORA</span></a>
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
            <a class="menu-item" data-section="dashboard" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a class="menu-item" data-section="events" href="{{ route('admin.dashboard') }}#events">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Event Management</span>
            </a>
            <a class="menu-item" data-section="categories" href="{{ route('admin.dashboard') }}#categories">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>
            </a>
            <a class="menu-item" data-section="tickets" href="{{ route('admin.dashboard') }}#tickets">
                <i class="fa-solid fa-ticket"></i>
                <span>Ticket Management</span>
            </a>
            <a class="menu-item {{ ($activeMenu ?? '') === 'seats' ? 'active' : '' }}" data-section="seats" href="{{ route('admin.seats.index') }}">
                <i class="fa-solid fa-chair"></i>
                <span>Seat Management</span>
            </a>

            <p class="menu-label">TRANSACTION</p>
            <a class="menu-item {{ ($activeMenu ?? '') === 'orders' ? 'active' : '' }}" data-section="orders" href="{{ route('admin.orders.index') }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Orders</span>
            </a>
            <a class="menu-item {{ ($activeMenu ?? '') === 'payments' ? 'active' : '' }}" data-section="payments" href="{{ route('admin.payments.index') }}">
                <i class="fa-solid fa-wallet"></i>
                <span>Payments</span>
            </a>
            <a class="menu-item {{ ($activeMenu ?? '') === 'ticket-validation' ? 'active' : '' }}" data-section="ticket-validation" href="{{ route('admin.qr-validation.index') }}">
                <i class="fa-solid fa-qrcode"></i>
                <span>QR Validation</span>
            </a>

            <p class="menu-label">MANAGEMENT</p>

            <a
                class="menu-item {{ ($activeMenu ?? '') === 'users' ? 'active' : '' }}"
                data-section="users"
                href="{{ route('admin.users.index') }}"
            >
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>

        <button
            class="menu-item"
            data-section="articles"
            onclick="showSection('articles')"
            type="button"
        >
            <i class="fa-solid fa-newspaper"></i>
            <span>Articles</span>
        </button>
        
            <button class="menu-item" data-section="reports" onclick="showSection('reports')" type="button">
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

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()" type="button">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <span class="topbar-label">TIXORA ADMIN</span>
                    <h1>{{ $title ?? 'Admin' }}</h1>
                </div>
            </div>
            <div class="topbar-right">
                <button class="notification-button" type="button">
                    <i class="fa-regular fa-bell"></i>
                    <span>3</span>
                </button>
                <div class="topbar-admin">
                    <div class="topbar-avatar">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')
    </main>
</body>
</html>
