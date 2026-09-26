@extends('layouts.eo', ['title' => $isEditing ? 'Edit Event' : 'Create Event'])

@section('content')
<div class="eo-page eo-event-form-page">
    <div class="eo-form-page-heading">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>{{ $isEditing ? 'Edit Event' : 'Create Event' }}</h2>
            <p>{{ $isEditing ? 'Perbarui informasi event sebelum diajukan untuk ditinjau.' : 'Lengkapi informasi event. Kamu dapat menyimpannya sebagai draft dan melanjutkan nanti.' }}</p>
        </div>
        <div class="eo-form-heading-actions">
            @if ($isEditing)
                <a class="eo-button eo-button-secondary" href="{{ route('eo.events.preview', $event) }}">Preview tersimpan</a>
            @endif
            <a class="eo-button eo-button-secondary" href="{{ route('eo.events.index') }}">Kembali ke Event Saya</a>
        </div>
    </div>

    @if ($isEditing && $event->approval_status === 'rejected' && filled($event->rejection_reason))
        <div class="eo-form-notice is-rejected"><strong>Event perlu diperbaiki</strong><p>{{ $event->rejection_reason }}</p></div>
    @endif

    @if ($errors->any())
        <div class="eo-form-notice is-error" role="alert">Periksa kembali data yang kamu masukkan.</div>
    @endif

    <form id="eoEventForm" class="eo-event-form" method="POST" action="{{ $isEditing ? route('eo.events.update', $event) : route('eo.events.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif

        <section class="eo-form-section">
            <div class="eo-form-section-heading"><span>01</span><div><h3>Informasi Dasar</h3><p>Nama, kategori, dan deskripsi event.</p></div></div>
            <div class="eo-form-grid">
                <label class="eo-field eo-field-full" for="name">Nama Event
                    <input id="name" name="name" type="text" maxlength="255" value="{{ old('name', $event->name) }}" required autocomplete="off">
                    @error('name')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field eo-field-full" for="category">Kategori
                    <input id="category" name="category" type="text" maxlength="100" value="{{ old('category', $event->category) }}" required>
                    @error('category')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field eo-field-full" for="description">Deskripsi
                    <textarea id="description" name="description" rows="5">{{ old('description', $event->description) }}</textarea>
                    @error('description')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </section>

        <section class="eo-form-section">
            <div class="eo-form-section-heading"><span>02</span><div><h3>Waktu &amp; Lokasi</h3><p>Tentukan tanggal dan lokasi penyelenggaraan.</p></div></div>
            <div class="eo-form-grid">
                <label class="eo-field eo-field-full" for="event_date">Tanggal Event
                    <input id="event_date" name="event_date" type="date" min="{{ today()->toDateString() }}" value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}" required>
                    @error('event_date')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field" for="venue">Venue
                    <input id="venue" name="venue" type="text" maxlength="255" value="{{ old('venue', $event->venue) }}" required>
                    @error('venue')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="eo-field" for="location">Lokasi
                    <input id="location" name="location" type="text" maxlength="255" value="{{ old('location', $event->location) }}" required>
                    @error('location')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </section>

        <section class="eo-form-section">
            <div class="eo-form-section-heading"><span>03</span><div><h3>Pengaturan Event</h3><p>Pilih jenis pengaturan tempat duduk.</p></div></div>
            <div class="eo-form-grid">
                <label class="eo-field eo-field-full" for="seating_type">Seating Type
                    <select id="seating_type" name="seating_type" required>
                        <option value="general_admission" @selected(old('seating_type', $event->seating_type) === 'general_admission')>General Admission</option>
                        <option value="numbered_seat" @selected(old('seating_type', $event->seating_type) === 'numbered_seat')>Numbered Seat</option>
                    </select>
                    @error('seating_type')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </section>

        <section class="eo-form-section">
            <div class="eo-form-section-heading"><span>04</span><div><h3>Cover Event</h3><p>Gunakan gambar JPG, PNG, atau WebP hingga 5 MB.</p></div></div>
            <div class="eo-cover-upload">
                <div class="eo-cover-preview" id="eoCoverPreview">
                    <span id="eoCoverFallback"><i class="fa-regular fa-image"></i><small>Preview cover event</small></span>
                    <img id="eoCoverImage" src="{{ $event->image ? asset('storage/' . $event->image) : '' }}" alt="Preview cover event" @if (! $event->image) hidden @endif>
                </div>
                <label class="eo-field" for="image">Upload image
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
                    @error('image')<small class="eo-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </section>

        <div class="eo-form-actions">
            <button class="eo-button eo-button-secondary" type="submit"><i class="fa-regular fa-floppy-disk"></i><span>Simpan Draft</span></button>
            <button class="eo-button eo-button-primary" type="button" id="eoShowLivePreview"><i class="fa-regular fa-eye"></i><span>Preview</span></button>
        </div>
    </form>

    <section class="eo-preview-card eo-live-preview" id="eoLivePreview" hidden aria-live="polite">
        <div class="eo-preview-cover">
            <span class="eo-cover-fallback"><i class="fa-regular fa-image"></i></span>
            <img id="eoLivePreviewImage" src="" alt="Preview cover event" hidden>
        </div>
        <div class="eo-preview-content">
            <span class="eo-event-category" id="eoLivePreviewCategory"></span>
            <h3 id="eoLivePreviewName"></h3>
            <p class="eo-preview-meta"><i class="fa-regular fa-calendar"></i><span id="eoLivePreviewDate"></span></p>
            <p class="eo-preview-meta"><i class="fa-solid fa-location-dot"></i><span id="eoLivePreviewPlace"></span></p>
            <p class="eo-preview-description" id="eoLivePreviewDescription"></p>
            <p class="eo-preview-seating" id="eoLivePreviewSeating"></p>
            <div class="eo-form-actions"><button class="eo-button eo-button-secondary" type="button" id="eoBackToForm">Kembali Edit</button></div>
        </div>
    </section>
</div>

<script>
    (() => {
        const form = document.getElementById('eoEventForm');
        const input = document.getElementById('image');
        const preview = document.getElementById('eoCoverImage');
        const fallback = document.getElementById('eoCoverFallback');
        const livePreview = document.getElementById('eoLivePreview');
        const setText = (id, value) => { document.getElementById(id).textContent = value; };
        let objectUrl = null;

        input.addEventListener('change', () => {
            if (objectUrl) URL.revokeObjectURL(objectUrl);
            const file = input.files?.[0];
            if (!file) return;
            objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
            preview.hidden = false;
            fallback.hidden = true;
        });
        preview.addEventListener('error', () => {
            preview.hidden = true;
            fallback.hidden = false;
        });

        document.getElementById('eoShowLivePreview').addEventListener('click', () => {
            const data = new FormData(form);
            const date = data.get('event_date');
            const seating = data.get('seating_type') === 'numbered_seat' ? 'Numbered Seat' : 'General Admission';
            setText('eoLivePreviewName', data.get('name') || 'Nama Event');
            setText('eoLivePreviewCategory', data.get('category') || 'Kategori');
            setText('eoLivePreviewDate', date ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(new Date(date + 'T00:00:00')) : 'Tanggal event');
            setText('eoLivePreviewPlace', [data.get('venue'), data.get('location')].filter(Boolean).join(' · ') || 'Venue · Lokasi');
            setText('eoLivePreviewDescription', data.get('description') || 'Belum ada deskripsi event.');
            setText('eoLivePreviewSeating', 'Seating Type: ' + seating);
            const image = document.getElementById('eoLivePreviewImage');
            image.src = preview.src;
            image.hidden = preview.hidden;
            form.hidden = true;
            livePreview.hidden = false;
            livePreview.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.getElementById('eoBackToForm').addEventListener('click', () => {
            livePreview.hidden = true;
            form.hidden = false;
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    })();
</script>
@endsection
