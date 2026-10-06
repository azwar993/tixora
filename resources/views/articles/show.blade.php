<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} | TIXORA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f7f7fa; color: #17171d; font-family: Inter, sans-serif; line-height: 1.6; }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1180px, 92%); margin: 0 auto; }
        .navbar { position: sticky; top: 0; z-index: 2; background: rgba(255,255,255,.96); border-bottom: 1px solid #ececf1; }
        .navbar-container { min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 28px; }
        .logo { color: #17171d; font-size: 22px; font-weight: 900; letter-spacing: -1px; }
        .logo span { color: #7257ff; }
        .nav-menu { display: flex; align-items: center; gap: 27px; }
        .nav-link { color: #666671; font-size: 12px; font-weight: 700; }
        .nav-link:hover, .nav-link.active { color: #7257ff; }
        .article-detail-section { min-height: 65vh; padding: 42px 0 76px; }
        .article-navigation { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 20px; margin-bottom: 22px; }
        .article-navigation a { display: inline-flex; align-items: center; gap: 8px; color: #7257ff; font-size: 11px; font-weight: 800; }
        .article-navigation a:hover { color: #5139d8; }
        .article-detail { overflow: hidden; border: 1px solid #e9e9ee; border-radius: 18px; background: #fff; }
        .article-header { max-width: 850px; margin: 0 auto; padding: 36px 34px 28px; }
        .article-header time { color: #85858f; font-size: 11px; }
        .article-header h1 { margin-top: 8px; font-size: clamp(28px, 4vw, 42px); line-height: 1.2; letter-spacing: -1.3px; overflow-wrap: anywhere; }
        .article-excerpt { margin-top: 14px; color: #777782; font-size: 15px; line-height: 1.7; }
        .article-cover img, .article-image-placeholder { display: block; width: 100%; max-height: 520px; object-fit: cover; }
        .article-image-placeholder { min-height: 240px; display: grid; place-items: center; background: #eeecf7; color: #9b91cf; font-size: 36px; }
        .article-body { max-width: 850px; min-height: 150px; margin: 0 auto; padding: 34px; color: #42424c; font-size: 15px; line-height: 1.9; white-space: pre-line; overflow-wrap: anywhere; }
        .footer { padding: 38px 0 18px; background: #17161d; color: #fff; }
        .footer-grid { display: grid; grid-template-columns: 1.5fr repeat(3, 1fr); gap: 28px; }
        .footer-brand p { max-width: 280px; margin-top: 10px; color: #b6b4c0; font-size: 11px; }
        .footer-column { display: flex; flex-direction: column; align-items: flex-start; gap: 7px; }
        .footer-column h2 { margin-bottom: 3px; font-size: 12px; }
        .footer-column a, .footer-column span { color: #b6b4c0; font-size: 10px; }
        .footer-column a:hover { color: #fff; }
        .footer-bottom { display: flex; justify-content: space-between; gap: 16px; margin-top: 26px; padding-top: 14px; border-top: 1px solid #34323d; color: #aaa8b4; font-size: 9px; }
        @media (max-width: 760px) {
            .nav-menu { gap: 14px; }
            .nav-link { font-size: 10px; }
            .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px) {
            .navbar-container { min-height: 64px; flex-wrap: wrap; gap: 3px 12px; padding: 10px 0; }
            .nav-menu { width: 100%; justify-content: space-between; gap: 8px; }
            .article-detail-section { padding: 28px 0 48px; }
            .article-header { padding: 27px 20px 22px; }
            .article-header h1 { font-size: 29px; }
            .article-excerpt { font-size: 13px; }
            .article-cover img { max-height: 300px; }
            .article-image-placeholder { min-height: 190px; }
            .article-body { padding: 25px 20px; font-size: 14px; }
            .footer-grid { gap: 22px 14px; }
            .footer-bottom { flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="container navbar-container">
            <a href="{{ route('home') }}" class="logo">TIX<span>ORA</span></a>
            <nav class="nav-menu" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="nav-link">Home</a>
                <a href="{{ route('home') . '#events' }}" class="nav-link">Events</a>
                <a href="{{ route('home') . '#categories' }}" class="nav-link">Categories</a>
                <a href="{{ route('articles.index') }}" class="nav-link active" aria-current="page">Articles</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="article-detail-section">
            <div class="container">
                <nav class="article-navigation" aria-label="Navigasi artikel">
                    <a href="{{ route('articles.index') }}">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Back to Articles
                    </a>
                    <a href="{{ route('home') }}">
                        <i class="fa-solid fa-house" aria-hidden="true"></i>
                        Back to Home
                    </a>
                </nav>

                <article class="article-detail">
                    <header class="article-header">
                        @if ($article->published_at)
                            <time datetime="{{ $article->published_at->toISOString() }}">
                                {{ $article->published_at->format('d M Y') }}
                            </time>
                        @endif
                        <h1>{{ $article->title }}</h1>
                        @if ($article->excerpt)
                            <p class="article-excerpt">{{ $article->excerpt }}</p>
                        @endif
                    </header>

                    <div class="article-cover">
                        @if ($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                        @else
                            <div class="article-image-placeholder" role="img" aria-label="Tidak ada gambar untuk {{ $article->title }}">
                                <i class="fa-regular fa-image" aria-hidden="true"></i>
                            </div>
                        @endif
                    </div>

                    <div class="article-body">{{ $article->content }}</div>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="logo">TIX<span>ORA</span></a>
                    <p>Platform pemesanan tiket event untuk menemukan pengalaman terbaik di JABODETABEK dan sekitarnya.</p>
                </div>
                <nav class="footer-column" aria-label="Platform">
                    <h2>Platform</h2>
                    <a href="{{ route('home') . '#events' }}">Events</a>
                    <a href="{{ route('home') . '#categories' }}">Categories</a>
                    <a href="{{ route('articles.index') }}">Articles</a>
                </nav>
                <div class="footer-column">
                    <h2>Explore</h2>
                    <a href="{{ route('home') }}">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}">My Ticket</a>
                    @endauth
                </div>
                <div class="footer-column">
                    <h2>Location</h2>
                    <span>Jakarta</span>
                    <span>Bogor</span>
                    <span>Depok</span>
                    <span>Tangerang</span>
                    <span>Bekasi</span>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2026 TIXORA. All Rights Reserved.</span>
                <span>Event Ticketing Platform</span>
            </div>
        </div>
    </footer>
</body>
</html>
