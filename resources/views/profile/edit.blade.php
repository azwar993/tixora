<x-app-layout>
    <x-slot name="title">Informasi Dasar | TIXORA</x-slot>

    <div class="account-center">
        <aside class="account-sidebar" aria-label="Navigasi akun">
            <a class="account-sidebar-brand" href="{{ route('home') }}" aria-label="TIXORA Home">
                TIX<span>ORA</span>
                <small>ACCOUNT CENTER</small>
            </a>

            <nav class="account-sidebar-nav">
                <span class="account-sidebar-label">PEMBELI</span>
                <a href="{{ route('home') }}#events"><i class="fa-solid fa-compass"></i><span>Jelajah Event</span></a>
                <a href="{{ route('dashboard') }}#tickets"><i class="fa-solid fa-ticket"></i><span>Tiket Saya</span></a>
                <a href="{{ route('dashboard') }}#orders"><i class="fa-solid fa-receipt"></i><span>Pesanan Saya</span></a>

                <span class="account-sidebar-label account-sidebar-label-spaced">AKUN</span>
                <a class="is-active" href="{{ route('profile.edit') }}" aria-current="page"><i class="fa-regular fa-id-card"></i><span>Informasi Dasar</span></a>
                <span class="account-sidebar-link is-disabled" aria-disabled="true"><i class="fa-solid fa-sliders"></i><span>Pengaturan</span><small>Segera</small></span>

                @if (auth()->user()->role === 'eo')
                    <span class="account-sidebar-label account-sidebar-label-spaced">MODE</span>
                    <a href="{{ route('eo.dashboard') }}"><i class="fa-solid fa-layer-group"></i><span>Event Creator</span></a>
                @endif
            </nav>

            <div class="account-sidebar-bottom">
                <span class="account-sidebar-avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'eo' ? 'Pembeli + Event Creator' : 'Akun Pembeli' }}</small></span>
            </div>
        </aside>

        <main class="account-content">
            <header class="account-content-heading">
                <div>
                    <span>AKUN TIXORA</span>
                    <h1>Informasi Dasar</h1>
                    <p>Kelola informasi dasar akun kamu.</p>
                </div>
            </header>

            <div class="account-content-surface">
                <section class="account-form-section" aria-labelledby="account-information-title">
                    <div class="account-section-heading">
                        <span class="account-section-icon"><i class="fa-regular fa-user"></i></span>
                        <div><h2 id="account-information-title">Informasi Akun</h2><p>Nama dan email yang digunakan pada akun TIXORA.</p></div>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </section>

                <section class="account-form-section" aria-labelledby="account-security-title">
                    <div class="account-section-heading">
                        <span class="account-section-icon"><i class="fa-solid fa-shield-halved"></i></span>
                        <div><h2 id="account-security-title">Keamanan Akun</h2><p>Ubah password untuk menjaga keamanan akun.</p></div>
                    </div>
                    @include('profile.partials.update-password-form')
                </section>

                <section class="account-form-section account-danger-section" aria-labelledby="account-danger-title">
                    <div class="account-section-heading">
                        <span class="account-section-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <div><h2 id="account-danger-title">Zona Berbahaya</h2><p>Permanen. Tindakan ini tidak dapat dibatalkan.</p></div>
                    </div>
                    @include('profile.partials.delete-user-form')
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
