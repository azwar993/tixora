@extends('layouts.eo', ['title' => 'Pengaturan'])

@section('content')
<div class="eo-page eo-settings-page">
    <section class="eo-events-intro">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Pengaturan</h2>
            <p>Perbarui informasi dasar akun creator kamu.</p>
        </div>
    </section>

    @if (session('success'))
        <div class="eo-form-notice is-success" role="status">{{ session('success') }}</div>
    @endif

    <section class="eo-panel eo-form-panel">
        <div class="eo-panel-heading">
            <div>
                <span class="eo-eyebrow">AKUN CREATOR</span>
                <h2>Informasi Akun</h2>
            </div>
        </div>
        <form method="POST" action="{{ route('eo.settings.update') }}">
            @csrf
            @method('PATCH')
            <div class="eo-form-grid">
                <label class="eo-field eo-field-full" for="creator-name">
                    Nama Creator
                    <input id="creator-name" name="name" type="text" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required>
                    @error('name')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field eo-field-full" for="creator-email">
                    Email
                    <input id="creator-email" type="email" value="{{ $user->email }}" autocomplete="email" readonly>
                    <small>Email akun ditampilkan dari profil pengguna dan tidak dapat diubah di halaman ini.</small>
                </label>
            </div>
            <div class="eo-form-actions">
                <button class="eo-button eo-button-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk"></i><span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
