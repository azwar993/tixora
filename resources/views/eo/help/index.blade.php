@extends('layouts.eo', ['title' => 'Bantuan'])

@section('content')
<div class="eo-page eo-help-page">
    <section class="eo-events-intro">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Bantuan</h2>
            <p>Panduan singkat untuk mengelola event di TIXORA.</p>
        </div>
    </section>

    <section class="eo-content-grid" aria-label="Panduan penggunaan TIXORA Creator">
        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">PANDUAN</span><h2>Cara membuat event</h2></div>
            </div>
            <p>Buka <strong>Event Saya</strong>, pilih <strong>Buat Event</strong>, lalu lengkapi nama, kategori, tanggal, lokasi, venue, dan deskripsi. Simpan sebagai draft untuk dilanjutkan nanti.</p>
        </article>

        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">TIKET</span><h2>Cara menambahkan Ticket Type</h2></div>
            </div>
            <p>Buka event dari daftar <strong>Event Saya</strong>, masuk ke <strong>Ticket Types</strong>, lalu pilih <strong>Tambah Ticket Type</strong>. Isi nama, harga, kuota, dan deskripsi tiket.</p>
        </article>

        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">NUMBERED SEATING</span><h2>Cara menggunakan numbered seating</h2></div>
            </div>
            <p>Pilih <strong>Numbered Seat</strong> saat membuat event. Setelah membuat Ticket Type, buka <strong>Kelola Seating</strong>, tambahkan section dan hubungkan ke Ticket Type, lalu generate kursi untuk baris yang diinginkan.</p>
        </article>

        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">APPROVAL</span><h2>Cara submit event untuk approval</h2></div>
            </div>
            <p>Lengkapi data event dan tambahkan minimal satu Ticket Type. Dari halaman event, buka preview lalu pilih <strong>Submit for Review</strong>. Event akan masuk ke antrean review Admin.</p>
        </article>

        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">PENJUALAN</span><h2>Cara melihat Penjualan</h2></div>
            </div>
            <p>Buka menu <strong>Penjualan</strong> untuk melihat order event milikmu. Gunakan filter event, status order, dan status pembayaran untuk mempersempit daftar.</p>
        </article>

        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">PESERTA</span><h2>Cara melihat Peserta</h2></div>
            </div>
            <p>Buka menu <strong>Peserta</strong> untuk melihat tiket yang sudah diterbitkan, status check-in, jenis tiket, dan kursi. Daftar dapat difilter berdasarkan event, status, atau pencarian.</p>
        </article>

        <article class="eo-panel eo-events-panel">
            <div class="eo-panel-heading">
                <div><span class="eo-eyebrow">STATUS EVENT</span><h2>Arti status event dan approval</h2></div>
            </div>
            <p><strong>Draft</strong> berarti event disimpan dan belum diajukan. <strong>Pending</strong> berarti menunggu review Admin. <strong>Approved</strong> berarti disetujui. <strong>Rejected</strong> berarti perlu diperbaiki sesuai catatan Admin sebelum diajukan kembali.</p>
            <p>Status jadwal berbeda dari approval: <strong>Coming Soon</strong>, <strong>On Going</strong>, dan <strong>Past Event</strong> menunjukkan tahap waktu event.</p>
        </article>
    </section>
</div>
@endsection
