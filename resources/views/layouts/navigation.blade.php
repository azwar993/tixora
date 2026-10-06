<nav class="tix-nav" aria-label="Navigasi utama">
    <div class="tix-nav-inner">
        <a class="tix-nav-logo" href="{{ route('home') }}" aria-label="TIXORA Home">TIX<span>ORA</span></a>

        <div class="tix-nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('home') }}#events">Events</a>
            <a href="{{ route('home') }}#categories">Categories</a>
            <a href="{{ route('articles.index') }}">Articles</a>
        </div>

        <div class="tix-nav-actions">
            @guest
                <a class="tix-login-link" href="{{ route('login') }}">Login</a>
                <a class="tix-register-link" href="{{ route('register') }}">Register</a>
            @else
                <details class="tix-account">
                    <summary aria-label="Menu akun {{ auth()->user()->name }}" aria-expanded="false" aria-controls="tixAccountMenu">
                        <span class="tix-account-avatar" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
                        <span class="tix-account-name">{{ auth()->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down tix-account-chevron" aria-hidden="true"></i>
                    </summary>
                    <div class="tix-account-menu" id="tixAccountMenu">
                        <div class="tix-account-identity">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>{{ auth()->user()->email }}</small>
                        </div>

                        @if (auth()->user()->role === 'admin')
                            <div class="tix-account-group-label">Admin</div>
                            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge-high"></i> Admin Dashboard</a>
                        @else
                            <div class="tix-account-group-label">Pembeli</div>
                            <a href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                            <a href="{{ route('dashboard') }}#tickets"><i class="fa-solid fa-ticket"></i> Tiket Saya</a>
                            <a href="{{ route('dashboard') }}#orders"><i class="fa-solid fa-receipt"></i> Pesanan Saya</a>

                            @if (auth()->user()->role === 'eo')
                                <div class="tix-account-divider"></div>
                                <div class="tix-account-group-label">Event Creator</div>
                                <a href="{{ route('eo.dashboard') }}"><i class="fa-solid fa-gauge-high"></i> Creator Dashboard</a>
                                <a href="{{ route('eo.events.index') }}"><i class="fa-regular fa-calendar"></i> Event Saya</a>
                            @endif
                        @endif

                        <div class="tix-account-divider"></div>
                        <a href="{{ route('profile.edit') }}"><i class="fa-regular fa-user"></i> Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
                        </form>
                    </div>
                </details>
            @endguest

            <details class="tix-mobile-navigation">
                <summary aria-label="Buka navigasi"><i class="fa-solid fa-bars"></i></summary>
                <div>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('home') }}#events">Events</a>
                    <a href="{{ route('home') }}#categories">Categories</a>
                    <a href="{{ route('articles.index') }}">Articles</a>
                </div>
            </details>
        </div>
    </div>
</nav>
