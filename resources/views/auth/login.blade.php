<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | TIXORA</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #0b0b10;
            color: #fff;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            background:
                radial-gradient(circle at 20% 20%, rgba(124, 58, 237, 0.18), transparent 28%),
                radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.12), transparent 25%),
                #0b0b10;
        }

        /* LEFT */

        .login-showcase {
            width: 48%;
            min-height: 100vh;
            padding: 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            background:
                linear-gradient(
                    135deg,
                    rgba(124, 58, 237, 0.16),
                    rgba(20, 20, 28, 0.15)
                );
            position: relative;
            overflow: hidden;
        }

        .login-showcase::before {
            content: "";
            width: 420px;
            height: 420px;
            position: absolute;
            top: -160px;
            left: -120px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.12);
            filter: blur(30px);
        }

        .brand {
            position: relative;
            z-index: 2;
            text-decoration: none;
            display: inline-block;
            width: fit-content;
        }

        .brand-main {
            font-size: 44px;
            font-weight: 900;
            letter-spacing: -2px;
            color: #ffffff;
        }

        .brand-main span {
            color: #7c3aed;
        }

        .brand-subtitle {
            margin-top: 6px;
            font-size: 12px;
            letter-spacing: 4px;
            color: #8888a0;
            font-weight: 700;
        }

        .showcase-content {
            max-width: 500px;
            position: relative;
            z-index: 2;
        }

        .showcase-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(124, 58, 237, 0.12);
            border: 1px solid rgba(124, 58, 237, 0.22);
            color: #b497ff;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .showcase-content h1 {
            margin: 0;
            font-size: clamp(42px, 5vw, 72px);
            line-height: 0.98;
            letter-spacing: -3px;
            font-weight: 900;
        }

        .showcase-content h1 span {
            color: #8b5cf6;
        }

        .showcase-content p {
            margin-top: 22px;
            margin-bottom: 34px;
            max-width: 450px;
            color: #9d9daf;
            line-height: 1.7;
            font-size: 16px;
        }

        .feature-list {
            display: grid;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #d9d9e2;
            font-size: 14px;
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: #17131f;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #a78bfa;
        }

        .showcase-footer {
            position: relative;
            z-index: 2;
            color: #656578;
            font-size: 12px;
        }

        /* RIGHT */

        .login-panel {
            width: 52%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-box {
            width: 100%;
            max-width: 440px;
        }

        .mobile-brand {
            display: none;
        }

        .login-heading {
            margin-bottom: 30px;
        }

        .login-heading .eyebrow {
            color: #8b5cf6;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .login-heading h2 {
            margin: 0;
            font-size: 34px;
            letter-spacing: -1px;
            font-weight: 800;
        }

        .login-heading p {
            margin-top: 10px;
            color: #858598;
            line-height: 1.6;
            font-size: 14px;
        }

        .session-message {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(34, 197, 94, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.16);
            color: #86efac;
            font-size: 13px;
        }

        .login-error {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.16);
            color: #fca5a5;
            font-size: 13px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            margin-bottom: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #dddde7;
        }

        .field-input {
            width: 100%;
            height: 54px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.10);
            background: #13131a;
            color: #fff;
            outline: none;
            transition: 0.2s ease;
        }

        .field-input::placeholder {
            color: #5d5d70;
        }

        .field-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
            background: #15151d;
        }

        .password-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 9px;
        }

        .password-head .field-label {
            margin-bottom: 0;
        }

        .forgot-link {
            color: #a78bfa;
            font-size: 12px;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #c4b5fd;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 4px 0 24px;
            color: #858598;
            font-size: 13px;
        }

        .remember input {
            width: 17px;
            height: 17px;
            accent-color: #7c3aed;
        }

        .login-button {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, #7c3aed, #8b5cf6);
            color: white;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: 0.2s ease;
            box-shadow: 0 12px 25px rgba(124, 58, 237, 0.20);
        }

        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(124, 58, 237, 0.28);
        }

        .register-link {
            margin-top: 26px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            color: #7f7f91;
            font-size: 13px;
        }

        .register-link a {
            color: #a78bfa;
            font-weight: 700;
            text-decoration: none;
        }

        .register-link a:hover {
            color: #c4b5fd;
        }

        .secure-note {
            margin-top: 22px;
            text-align: center;
            color: #4f4f60;
            font-size: 11px;
        }

        @media (max-width: 900px) {
            .login-showcase {
                width: 42%;
                padding: 36px;
            }

            .login-panel {
                width: 58%;
                padding: 28px;
            }

            .showcase-content h1 {
                font-size: 48px;
            }
        }

        @media (max-width: 700px) {
            .login-page {
                display: block;
            }

            .login-showcase {
                display: none;
            }

            .login-panel {
                width: 100%;
                min-height: 100vh;
                padding: 24px 18px;
            }

            .mobile-brand {
                display: block;
                text-align: center;
                margin-bottom: 30px;
            }

            .mobile-brand .brand-main {
                font-size: 36px;
            }

            .login-heading h2 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>

<div class="login-page">

    <!-- LEFT -->
    <section class="login-showcase">

        <a href="{{ route('home') }}" class="brand">
            <div class="brand-main">
                TIX<span>ORA</span>
            </div>

            <div class="brand-subtitle">
                EVENT TICKETING PLATFORM
            </div>
        </a>

        <div class="showcase-content">

            <div class="showcase-badge">
                ✦ PLATFORM EVENT DIGITAL
            </div>

            <h1>
                Temukan event.<br>
                <span>Rasakan momennya.</span>
            </h1>

            <p>
                Beli tiket event favoritmu atau mulai buat
                event sendiri melalui TIXORA.
            </p>

            <div class="feature-list">

                <div class="feature-item">
                    <div class="feature-icon">🎫</div>
                    Beli tiket event dengan mudah
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🎟️</div>
                    Buat dan kelola event sendiri
                </div>

                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    Pantau event dan penjualan
                </div>

            </div>

        </div>

        <div class="showcase-footer">
            © {{ date('Y') }} TIXORA. All rights reserved.
        </div>

    </section>


    <!-- RIGHT -->
    <section class="login-panel">

        <div class="login-box">

            <div class="mobile-brand">

                <a href="{{ route('home') }}" class="brand">
                    <div class="brand-main">
                        TIX<span>ORA</span>
                    </div>
                </a>

            </div>


            <div class="login-heading">

                <div class="eyebrow">
                    LOGIN
                </div>

                <h2>
                    Selamat datang kembali 👋
                </h2>

                <p>
                    Masuk ke akun TIXORA untuk melanjutkan.
                </p>

            </div>


            @if (session('status'))
                <div class="session-message">
                    {{ session('status') }}
                </div>
            @endif


            @if ($errors->any())
                <div class="login-error">
                    Email atau password yang kamu masukkan salah.
                </div>
            @endif


            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- EMAIL -->
                <div class="field">

                    <label
                        for="email"
                        class="field-label"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        class="field-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        required
                        autofocus
                        autocomplete="username"
                    >

                    @error('email')
                        <div class="mt-2 text-xs text-red-400">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- PASSWORD -->
                <div class="field">

                    <div class="password-head">

                        <label
                            for="password"
                            class="field-label"
                        >
                            Password
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="forgot-link"
                            >
                                Lupa password?
                            </a>
                        @endif

                    </div>

                    <input
                        id="password"
                        class="field-input"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                    >

                    @error('password')
                        <div class="mt-2 text-xs text-red-400">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- REMEMBER -->
                <label class="remember">

                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                    >

                    <span>
                        Ingat saya
                    </span>

                </label>


                <!-- BUTTON -->
                <button
                    type="submit"
                    class="login-button"
                >
                    Masuk ke TIXORA
                </button>

            </form>


            <!-- REGISTER -->
            <div class="register-link">

                Belum punya akun?

                <a href="{{ route('register') }}">
                    Daftar sekarang
                </a>

            </div>


            <div class="secure-note">
                Data akun kamu dilindungi oleh sistem autentikasi TIXORA.
            </div>

        </div>

    </section>

</div>

</body>
</html>