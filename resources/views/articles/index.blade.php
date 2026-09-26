<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles | TIXORA</title>
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
        .articles-section { min-height: 65vh; padding: 64px 0 78px; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 30px; }
        .section-label { color: #7257ff; font-size: 10px; font-weight: 900; letter-spacing: 2px; }
        .section-heading h1 { margin-top: 6px; font-size: clamp(30px, 4vw, 42px); line-height: 1.15; letter-spacing: -1.5px; }
        .section-heading p { margin-top: 9px; color: #85858f; font-size: 13px; }
        .home-button { display: inline-flex; align-items: center; gap: 8px; flex: 0 0 auto; padding: 10px 15px; border-radius: 8px; background: #f0edff; color: #7257ff; font-size: 11px; font-weight: 800; }
        .home-button:hover { background: #e6e0ff; }
        .article-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 19px; }
        .article-card { overflow: hidden; border: 1px solid #e9e9ee; border-radius: 17px; background: #fff; }
        .article-card img, .article-image-placeholder { display: block; width: 100%; height: 205px; object-fit: cover; }
        .article-image-placeholder { display: grid; place-items: center; background: #eeecf7; color: #9b91cf; font-size: 30px; }
        .article-content { padding: 19px 20px 21px; }
        .article-content time { color: #85858f; font-size: 10px; }
        .article-content h2 { margin-top: 7px; font-size: 17px; line-height: 1.35; }
        .article-content p { margin-top: 7px; color: #86868f; font-size: 12px; }
        .article-content a { display: inline-flex; align-items: center; gap: 8px; margin-top: 14px; color: #7257ff; font-size: 11px; font-weight: 900; }
        .article-content a:hover { color: #5139d8; }
        .empty-state { padding: 48px 20px; border: 1px dashed #d9d6e7; border-radius: 16px; background: #fff; text-align: center; }
        .empty-state i { color: #8875df; font-size: 25px; }
        .empty-state h2 { margin-top: 12px; font-size: 18px; }
        .empty-state p { margin-top: 5px; color: #85858f; font-size: 12px; }
        .pagination { margin-top: 28px; }
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
            .article-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px) {
            .navbar-container { min-height: 64px; flex-wrap: wrap; gap: 3px 12px; padding: 10px 0; }
            .nav-menu { width: 100%; justify-content: space-between; gap: 8px; }
            .articles-section { padding: 38px 0 52px; }
            .section-heading { align-items: flex-start; flex-direction: column; margin-bottom: 22px; }
            .article-grid { grid-template-columns: 1fr; gap: 14px; }
            .article-card img, .article-image-placeholder { height: 190px; }
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
        <section class="articles-section">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <span class="section-label">TIXORA JOURNAL</span>
                        <h1>Articles</h1>
                        <p>Informasi, panduan, dan cerita seputar pengalaman event bersama TIXORA.</p>
                    </div>
                    <a class="home-button" href="{{ route('home') }}">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to Home
                    </a>
                </div>

                @forelse ($articles as $article)
                    @if ($loop->first)
                        <div class="article-grid">
                    @endif
                    <article class="article-card">
                        @if ($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" loading="lazy">
                        @else
                            <div class="article-image-placeholder" role="img" aria-label="Tidak ada gambar untuk {{ $article->title }}">
                                <i class="fa-regular fa-image" aria-hidden="true"></i>
                            </div>
                        @endif
                        <div class="article-content">
                            @if ($article->published_at)
                                <time datetime="{{ $article->published_at->toISOString() }}">
                                    {{ $article->published_at->format('d M Y') }}
                                </time>
                            @endif
                            <h2>{{ $article->title }}</h2>
                            @if ($article->excerpt)
                                <p>{{ $article->excerpt }}</p>
                            @endif
                            <a href="{{ route('articles.show', $article) }}">
                                Baca Artikel
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                    @if ($loop->last)
                        </div>
                    @endif
                @empty
                    <div class="empty-state">
                        <i class="fa-regular fa-newspaper" aria-hidden="true"></i>
                        <h2>Belum ada artikel yang dipublikasikan</h2>
                        <p>Artikel terbaru TIXORA akan tampil di sini.</p>
                    </div>
                @endforelse

                @if ($articles->count() > 0)
                    <div class="pagination">
                        {{ $articles->links() }}
                    </div>
                @endif
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
