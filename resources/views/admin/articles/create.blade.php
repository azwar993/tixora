@extends('layouts.admin', ['title' => 'Create Article', 'activeMenu' => 'articles'])

@section('content')
    <section class="admin-section active orders-page">
        <div class="section-top">
            <div>
                <span class="topbar-label">CONTENT</span>
                <h2>Create Article</h2>
                <p>Buat artikel baru untuk publikasi konten TIXORA.</p>
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

            <form class="admin-form" method="POST" action="{{ route('admin.articles.store') }}">
                @csrf

                <label for="title">Title</label>
                <input
                    id="title"
                    name="title"
                    type="text"
                    value="{{ old('title') }}"
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
                    value="{{ old('slug') }}"
                    maxlength="255"
                    placeholder="contoh-judul-artikel"
                >
                <small>Kosongkan agar slug dibuat otomatis dari title.</small>
                @error('slug')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="excerpt">Excerpt <small>(optional)</small></label>
                <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="content">Content</label>
                <textarea id="content" name="content" rows="12" required>{{ old('content') }}</textarea>
                @error('content')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="image">Image path <small>(optional)</small></label>
                <input
                    id="image"
                    name="image"
                    type="text"
                    value="{{ old('image') }}"
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
                            <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                            <option value="published" @selected(old('status') === 'published')>Published</option>
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
                            value="{{ old('published_at') }}"
                        >
                        <small>Jika status Published dan waktunya kosong, controller akan mengisi waktu saat ini.</small>
                        @error('published_at')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="modal-actions">
                    <a class="secondary-button" href="{{ route('admin.articles.index') }}">Cancel</a>
                    <button class="primary-button" type="submit">
                        <i class="fa-solid fa-plus"></i>
                        Create Article
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
