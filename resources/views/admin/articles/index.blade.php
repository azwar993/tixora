@extends('layouts.admin', ['title' => 'Articles', 'activeMenu' => 'articles'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">CONTENT</span>
                <h2>Articles</h2>
                <p>Kelola artikel dan publikasi konten TIXORA.</p>
            </div>

            <div>
                <a class="secondary-button" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-arrow-left"></i>
                    Dashboard
                </a>
                <a class="primary-button" href="{{ route('admin.articles.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    Create Article
                </a>
            </div>
        </div>

        <div class="filter-bar">
            <span>Filter status</span>
            <a
                class="{{ request('status') === null || request('status') === '' ? 'primary-button' : 'secondary-button' }}"
                href="{{ route('admin.articles.index') }}"
                @if (request('status') === null || request('status') === '') aria-current="page" @endif
            >All</a>
            <a
                class="{{ request('status') === 'draft' ? 'primary-button' : 'secondary-button' }}"
                href="{{ route('admin.articles.index', ['status' => 'draft']) }}"
                @if (request('status') === 'draft') aria-current="page" @endif
            >Draft</a>
            <a
                class="{{ request('status') === 'published' ? 'primary-button' : 'secondary-button' }}"
                href="{{ route('admin.articles.index', ['status' => 'published']) }}"
                @if (request('status') === 'published') aria-current="page" @endif
            >Published</a>
        </div>

        <div class="panel orders-table-panel">
            <div class="table-wrapper orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ARTICLE</th>
                            <th>STATUS</th>
                            <th>PUBLISHED</th>
                            <th>CREATED</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($articles as $article)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        @if ($article->image)
                                            <img
                                                src="{{ asset('storage/' . $article->image) }}"
                                                alt=""
                                                width="52"
                                                height="52"
                                                style="width: 52px; height: 52px; object-fit: cover; border-radius: 8px;"
                                            >
                                        @else
                                            <span
                                                aria-hidden="true"
                                                style="width: 52px; height: 52px; flex: 0 0 52px; display: grid; place-items: center; border-radius: 8px; background: #f0edff; color: #7257ff;"
                                            >
                                                <i class="fa-regular fa-image"></i>
                                            </span>
                                        @endif
                                        <strong>{{ $article->title }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill {{ $article->status === 'published' ? 'green' : 'orange' }}">
                                        {{ ucfirst($article->status) }}
                                    </span>
                                </td>
                                <td>{{ $article->published_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td>{{ $article->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a
                                            class="action-button"
                                            href="{{ route('admin.articles.edit', $article) }}"
                                            title="Edit article"
                                            aria-label="Edit {{ $article->title }}"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        @if ($article->status === 'draft')
                                            <form method="POST" action="{{ route('admin.articles.publish', $article) }}">
                                                @csrf
                                                <button type="submit" title="Publish article" aria-label="Publish {{ $article->title }}">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form
                                            method="POST"
                                            action="{{ route('admin.articles.destroy', $article) }}"
                                            onsubmit="return confirm('Hapus artikel ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete article" aria-label="Delete {{ $article->title }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="orders-empty-state">
                                        <div class="orders-empty-icon">
                                            <i class="fa-regular fa-newspaper"></i>
                                        </div>
                                        <h3>Belum ada artikel</h3>
                                        <p>Mulai kelola publikasi konten TIXORA dengan membuat artikel pertama.</p>
                                        <a class="primary-button" href="{{ route('admin.articles.create') }}">
                                            <i class="fa-solid fa-plus"></i>
                                            Create Article
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $articles->links() }}
    </section>
@endsection
