<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Creator Overview' }} | TIXORA Creator</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite('resources/css/eo.css')
</head>
<body class="eo-body">
    <div class="eo-shell">
        <aside class="eo-sidebar" id="eoSidebar" aria-label="Creator navigation">
            <a class="eo-brand" href="{{ route('eo.dashboard') }}">
                <span class="eo-brand-mark">T</span>
                <span class="eo-brand-copy">
                    <strong>TIXORA</strong>
                    <small>CREATOR</small>
                </span>
            </a>

            <div class="eo-sidebar-account">
                <span class="eo-avatar" aria-hidden="true"><i class="fa-solid fa-user-astronaut"></i></span>
                <span class="eo-account-copy">
                    @auth
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Event Organizer</small>
                    @endauth
                </span>
            </div>

            <nav class="eo-menu" aria-label="Creator menu">
                <span class="eo-menu-label">WORKSPACE</span>
                <a class="eo-menu-item {{ request()->routeIs('eo.dashboard') ? 'active' : '' }}" href="{{ route('eo.dashboard') }}" @if(request()->routeIs('eo.dashboard')) aria-current="page" @endif>
                    <i class="fa-solid fa-chart-pie"></i><span>Dashboard</span>
                </a>
                <a class="eo-menu-item {{ request()->routeIs('eo.events.*') ? 'active' : '' }}" href="{{ route('eo.events.index') }}" @if(request()->routeIs('eo.events.*')) aria-current="page" @endif>
                    <i class="fa-solid fa-calendar-days"></i><span>Event Saya</span>
                </a>
                <a class="eo-menu-item {{ request()->routeIs('eo.sales.*') ? 'active' : '' }}" href="{{ route('eo.sales.index') }}" @if(request()->routeIs('eo.sales.*')) aria-current="page" @endif>
                    <i class="fa-solid fa-chart-line"></i><span>Penjualan</span>
                </a>
                <a class="eo-menu-item {{ request()->routeIs('eo.participants.*') ? 'active' : '' }}" href="{{ route('eo.participants.index') }}" @if(request()->routeIs('eo.participants.*')) aria-current="page" @endif>
                    <i class="fa-solid fa-users"></i><span>Peserta</span>
                </a>

                <span class="eo-menu-label eo-menu-label-spaced">AKUN & AKTIVITAS</span>
                <a class="eo-menu-item {{ request()->routeIs('eo.activity.*') ? 'active' : '' }}" href="{{ route('eo.activity.index') }}" @if(request()->routeIs('eo.activity.*')) aria-current="page" @endif>
                    <i class="fa-solid fa-bolt"></i><span>Aktivitas</span>
                </a>
                <a class="eo-menu-item {{ request()->routeIs('eo.settings.*') ? 'active' : '' }}" href="{{ route('eo.settings.edit') }}" @if(request()->routeIs('eo.settings.*')) aria-current="page" @endif>
                    <i class="fa-solid fa-gear"></i><span>Pengaturan</span>
                </a>
                <a class="eo-menu-item {{ request()->routeIs('eo.help.*') ? 'active' : '' }}" href="{{ route('eo.help.index') }}" @if(request()->routeIs('eo.help.*')) aria-current="page" @endif>
                    <i class="fa-regular fa-circle-question"></i><span>Bantuan</span>
                </a>
            </nav>

            <div class="eo-sidebar-bottom">
                @auth
                    <a class="eo-profile-link" href="{{ route('profile.edit') }}">
                        <i class="fa-regular fa-user"></i><span>Akun EO</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="eo-logout" type="submit">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i><span>Keluar</span>
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <button class="eo-sidebar-backdrop" id="eoSidebarBackdrop" type="button" aria-label="Tutup menu"></button>

        <main class="eo-main">
            <header class="eo-topbar">
                <div class="eo-topbar-start">
                    <button class="eo-menu-toggle" id="eoMenuToggle" type="button" aria-label="Buka menu" aria-controls="eoSidebar" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <p class="eo-breadcrumb"><span>Creator</span><i class="fa-solid fa-chevron-right"></i> {{ request()->routeIs('eo.sales.*') ? 'Penjualan' : (request()->routeIs('eo.participants.*') ? 'Peserta' : (request()->routeIs('eo.activity.*') ? 'Aktivitas' : (request()->routeIs('eo.settings.*') ? 'Pengaturan' : (request()->routeIs('eo.help.*') ? 'Bantuan' : (request()->routeIs('eo.events.*') ? 'Event Saya' : 'Overview'))))) }}</p>
                        <h1>{{ $title ?? 'Overview' }}</h1>
                    </div>
                </div>

                <div class="eo-topbar-actions">
                    <a class="eo-switch-buyer" href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-store"></i><span>Beralih ke Pembeli</span>
                    </a>
                    <a class="eo-button eo-button-primary" href="{{ route('eo.events.create') }}">
                        <i class="fa-solid fa-plus"></i><span>Buat Event</span>
                    </a>
                    @auth
                        <a class="eo-topbar-account" href="{{ route('profile.edit') }}">
                            <span class="eo-avatar eo-avatar-light" aria-hidden="true"><i class="fa-solid fa-user-astronaut"></i></span>
                            <span class="eo-topbar-account-copy"><strong>{{ auth()->user()->name }}</strong><small>Akun EO</small></span>
                            <i class="fa-solid fa-chevron-down eo-account-chevron"></i>
                        </a>
                    @endauth
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <script>
        (() => {
            const sidebar = document.getElementById('eoSidebar');
            const toggle = document.getElementById('eoMenuToggle');
            const backdrop = document.getElementById('eoSidebarBackdrop');

            const setMenuOpen = (isOpen) => {
                sidebar.classList.toggle('is-open', isOpen);
                backdrop.classList.toggle('is-visible', isOpen);
                toggle.setAttribute('aria-expanded', String(isOpen));
            };

            toggle.addEventListener('click', () => {
                setMenuOpen(!sidebar.classList.contains('is-open'));
            });
            backdrop.addEventListener('click', () => setMenuOpen(false));
            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setMenuOpen(false));
            });
            window.addEventListener('resize', () => {
                if (window.innerWidth > 900) setMenuOpen(false);
            });
        })();
    </script>
</body>
</html>
