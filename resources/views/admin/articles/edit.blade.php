@extends('layouts.admin', ['title' => 'Edit Article', 'activeMenu' => 'articles'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">CONTENT</span>
                <h2>Edit Article</h2>
                <p>Perbarui informasi dan publikasi artikel TIXORA.</p>
            </div>

            <a class="secondary-button" href="{{ route('admin.articles.index') }}">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Articles
            </a>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <span>ARTICLE DETAILS</span>
                    <h3>Informasi Artikel</h3>
                </div>
            </div>

            <form class="admin-form" method="POST" action="{{ route('admin.articles.update', $article) }}">
                @csrf
                @method('PUT')

                <label for="title">Title</label>
                <input
                    id="title"
                    name="title"
                    type="text"
                    value="{{ old('title', $article->title) }}"
                    maxlength="255"
                    required
                    autofocus
                >
                @error('title')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="slug">Slug <small>(optional)</small></label>
                <input
                    id="slug"
                    name="slug"
                    type="text"
                    value="{{ old('slug', $article->slug) }}"
                    maxlength="255"
                    placeholder="contoh-judul-artikel"
                >
                <small>Kosongkan agar slug dibuat otomatis dari title.</small>
                @error('slug')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="excerpt">Excerpt <small>(optional)</small></label>
                <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
                @error('excerpt')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="content">Content</label>
                <textarea id="content" name="content" rows="12" required>{{ old('content', $article->content) }}</textarea>
                @error('content')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="image">Image path <small>(optional)</small></label>
                @if ($article->image)
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <img
                            src="{{ asset('storage/' . $article->image) }}"
                            alt=""
                            width="64"
                            height="64"
                            style="width: 64px; height: 64px; object-fit: cover; border-radius: 8px;"
                        >
                        <span>{{ $article->image }}</span>
                    </div>
                @endif
                <input
                    id="image"
                    name="image"
                    type="text"
                    value="{{ old('image', $article->image) }}"
                    maxlength="255"
                    placeholder="articles/nama-gambar.jpg"
                >
                <small>Masukkan path gambar yang sudah tersedia, misalnya articles/nama-gambar.jpg.</small>
                @error('image')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <div class="form-row">
                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="draft" @selected(old('status', $article->status) === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $article->status) === 'published')>Published</option>
                        </select>
                        @error('status')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label for="published_at">Published at <small>(optional)</small></label>
                        <input
                            id="published_at"
                            name="published_at"
                            type="datetime-local"
                            value="{{ old('published_at', $article->published_at?->format('Y-m-d\\TH:i')) }}"
                        >
                        <small>Jika status Published dan waktunya kosong, controller akan mempertahankan waktu yang ada atau mengisi waktu saat ini.</small>
                        @error('published_at')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="modal-actions">
                    <a class="secondary-button" href="{{ route('admin.articles.index') }}">Cancel</a>
                    <button class="primary-button" type="submit">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
