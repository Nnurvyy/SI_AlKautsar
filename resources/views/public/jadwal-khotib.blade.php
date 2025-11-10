@extends('layouts.public')

@section('title', 'Jadwal Khutbah Jumat')

@push('styles')
{{-- Saya HAPUS link Swiper CSS karena tidak dipakai di halaman ini --}}
<style>
    /* Style untuk Card Kajian Rutin (Lama - tidak terpakai) */
    .kajian-list-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        overflow: hidden; 
    }
    .kajian-avatar {
        width: 90px;
        height: 90px;
        object-fit: cover;
    }
    .kajian-info {
        padding: 0.75rem 1rem;
    }
    .kajian-info h6 {
        font-size: 1rem;
    }
    .kajian-info p {
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }
    /* ... (akhir style lama) ... */


    /* ================================================= */
    /* == STYLE JUDUL (DARI HALAMAN KAJIAN) == */
    /* ================================================= */

    .donasi-title-heading {
        font-family: 'Poppins', sans-serif; 
        font-weight: 700;
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    /* ================================================= */
    /* == STYLE CARD UTAMA (dipakai untuk Khutbah Jumat Ini) == */
    /* ================================================= */

    /* Nama class .kajian-event-card kita pakai ulang saja ya */
    .kajian-event-card {
        width: 100%;
        border-radius: 16px; /* Tepi tumpul */
        overflow: hidden; /* Penting agar gambar tidak "bocor" */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: none;
        background-color: #ffffff; /* Card jadi putih polos */
    }
    .kajian-event-img {
        width: 100%;
        height: 320px; /* Tinggi gambar dibuat tetap agar seragam */
        object-fit: cover;
    }
    
    .kajian-event-content {
        padding: 1rem; /* Padding standar untuk card-body */
    }
    
    /* Style Teks di dalam Card Utama */
    .kajian-event-content h6 {
        font-weight: 700;
        font-size: 1.25rem; /* Ukuran diperbesar */
        color: #212529; 
        margin-bottom: 0.25rem;
    }
    .kajian-event-content p {
        font-size: 1.0rem; /* Ukuran diperbesar */
        color: #495057; 
        margin-bottom: 0.5rem; /* Tambah margin bawah agar ada jarak ke badge */
        line-height: 1.4;
    }
    /* Override text-muted dan small */
    .kajian-event-content .text-muted,
    .kajian-event-content .small {
        color: #495057 !important; 
        font-size: 1.0rem; /* Ukuran diperbesar */
    }
    /* Style Badge di dalam card (misal: badge "Jumat Ini") */
    .kajian-event-content .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.4em 0.8em;
        background-color: #0d6efd !important; /* Biru (ganti dari oranye) */
        color: #ffffff !important;
        text-shadow: none;
        border-radius: 6px;
    }
    .kajian-event-content p.mb-1 {
        margin-bottom: 0.5rem !important;
    }

    
    /* ======================================================== */
    /* == STYLE BADGE TANGGAL == */
    /* ======================================================== */
    
    /* Ini adalah style dasar badge tanggal (dipakai oleh list "Selanjutnya") */
    .tanggal-badge {
        font-size: 0.9rem; /* Ukuran diperbesar */
        font-weight: 600;
        padding: 0.5em 0.75em;
        background-color: #343a40; /* Warna Netral (Abu Gelap) */
        color: #ffffff; 			/* Teks putih */
        border-radius: 8px;
        display: inline-block; 
        margin-top: 0.5rem; 
    }

    /* Ini override warna untuk badge tanggal di card UTAMA (biar serasi) */
    .kajian-event-content .tanggal-badge {
        background-color: #6c757d; /* Warna abu-abu (Bootstrap secondary) */
        color: #ffffff;
    }


    /* ================================================= */
    /* == STYLE LIST (dipakai untuk Khutbah Selanjutnya) == */
    /* ================================================= */
    .kajian-list-card-new {
        border: none;
        border-radius: 12px; 
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); 
        margin-bottom: 1rem;
        overflow: hidden; 
        background: linear-gradient(to bottom right, #ffffff, #aaadaf);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .kajian-list-card-new:hover {
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12); 
    }
    .kajian-list-card-new .card-img {
        object-fit: cover;
        height: 100%; 
        min-height: 140px; 
        border-radius: 12px 0 0 12px !important; 
    }
    .kajian-list-card-new .card-body {
        padding: 0.75rem 1rem; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; 
    }
    .kajian-list-card-new .card-title {
        font-size: 0.95rem; 
        font-weight: 700;
        color: #212529; 
        line-height: 1.3;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical; 	
        overflow: hidden;
    }
    .kajian-list-card-new .card-text-tema {
        font-size: 0.85rem;
        color: #495057; 
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
</style>
@endpush

@section('content')

{{-- ================================================== --}}
{{-- == BAGIAN KHUTBAH JUMAT INI (CARD BESAR) == --}}
{{-- ================================================== --}}
<div class="pt-3 pb-2">
    <div class="container">
        <div class="container pt-4 pb-2">
            <h2 class="donasi-title-heading">
                Khutbah Jumat
            </h2>
        </div>

        {{-- 
          Ini adalah card utamanya. 
          Tidak ada Swiper, hanya 1 card.
          Nanti Anda bisa ganti data dummy ini dengan data asli, 
          misalnya @if($khutbahJumat) ... @else ... @endif 
        --}}

        {{-- DUMMY DATA UNTUK JUMAT INI --}}
        <div class="kajian-event-card">
            
            {{-- 1. FOTO --}}
            <img src="{{ asset('images/events/abdul-somad.jpg') }}" alt="Ustadz Abdul Somad"
                 class="kajian-event-img">
            
            {{-- 2. KONTEN --}}
            <div class="kajian-event-content">
                <span class="badge mb-2">Jumat Ini</span>
                <h6 class="fw-bold">Khotib: Ustadz Abdul Somad, Lc., MA.</h6>
                <p class="text-muted small mb-1">Imam: Ustadz Muzammil Hasballah</p>
                <p class="text-muted small mb-1">Tema: "Makna Kemerdekaan Hakiki"</p>
                
                {{-- Badge Tanggal (Warna abu-abu #6c757d) --}}
                <span class="tanggal-badge">
                    14 Nov 2025, Pukul 12:00
                </span>
            </div>
        </div>
        
        {{-- UNTUK KONDISI JIKA DATA KOSONG --}}
        {{--
        <div class="kajian-event-card" style="height: 380px; display: flex; justify-content: center; align-items: center; background: #f8f9fa;">
            <div class="text-center">
                <img src="{{ asset('images/icons/khutbah-jumat.png') }}" alt="Khutbah" style="width: 80px; height: 80px; opacity: 0.5;" class="mx-auto mb-3">
                <p class="text-muted">Belum ada jadwal khutbah terbaru.</p>
            </div>
        </div>
        --}}

    </div>
</div>

{{-- ================================================== --}}
{{-- == BAGIAN KHUTBAH SELANJUTNYA (LIST) == --}}
{{-- ================================================== --}}
<div class="feature-section-bg py-4">
    <div class="container">
        <h2 class="donasi-title-heading" style="font-size: 1.5rem; margin-bottom: 1rem;">Khutbah Selanjutnya</h2>
        
        {{-- 
          Ini adalah daftar list-nya.
          Nanti Anda bisa ganti data dummy ini dengan @forelse($khutbahSelanjutnya as $khotib) ... @empty ... @endforelse 
        --}}

        {{-- DUMMY DATA 1 --}}
        <div class="card kajian-list-card-new">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{ asset('images/events/hannan-attaki.jpeg') }}" class="card-img" alt="Foto Ustadz Hannan Attaki">
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">Khotib: Ustadz Hannan Attaki, Lc.</h5>
                            <p class="card-text-tema">
                                Imam: Ustadz Taqy Malik
                            </p>
                        </div>
                        <div>
                            {{-- Badge Tanggal (Warna abu gelap #343a40) --}}
                            <span class="tanggal-badge">
                                21 Nov 2025
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DUMMY DATA 2 --}}
        <div class="card kajian-list-card-new">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{ asset('images/events/adi-hidayat.jpg') }}" class="card-img" alt="Foto Ustadz Adi Hidayat">
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">Khotib: Ustadz Adi Hidayat, Lc., MA.</h5>
                            <p class="card-text-tema">
                                Imam: Ustadz Salim Bahanan
                            </p>
                        </div>
                        <div>
                            <span class="tanggal-badge">
                                28 Nov 2025
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- UNTUK KONDISI JIKA DATA KOSONG --}}
        {{--
        <div class="alert alert-info text-center">
            Belum ada jadwal khutbah selanjutnya.
        </div>
        --}}
    </div>
</div>
@endsection

@push('scripts')
{{-- TIDAK ADA JAVASCRIPT --}}
{{-- Saya HAPUS script Swiper karena tidak dipakai di halaman ini --}}
@endpush