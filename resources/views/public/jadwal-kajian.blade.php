@extends('layouts.public')

@section('title', 'Jadwal Kajian')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* Style untuk Card Kajian Rutin (Lama - tidak terpakai) */
    /* ... (style lama Anda) ... */
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
    /* == STYLE JUDUL (DARI DONASI) == */
    /* ================================================= */

    .donasi-title-heading {
        font-family: 'Poppins', sans-serif; 
        font-weight: 700;
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    /* ================================================= */
    /* == STYLE KAJIAN EVENT (VERSI BARU - Teks di Bawah) == */
    /* ================================================= */

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
    
    /* Style Teks di dalam Card Event (WARNA BARU) */
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
    /* Style Badge di dalam card */
    .kajian-event-content .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.4em 0.8em;
        background-color: #0d6efd !important; 
        color: #ffffff !important;
        text-shadow: none;
        border-radius: 6px;
    }
    /* Margin-bottom untuk paragraf tema (mb-1) di HTML akan jadi kecil, 
       jadi kita paskan di sini */
    .kajian-event-content p.mb-1 {
        margin-bottom: 0.5rem !important;
    }

    
    /* ======================================================== */
    /* == PERUBAHAN WARNA BADGE TANGGAL KHUSUS KAJIAN EVENT == */
    /* ======================================================== */
    .kajian-event-content .tanggal-badge {
        background-color: #6c757d; /* Warna abu-abu (Bootstrap secondary) */
        color: #ffffff;
    }


    /* ================================================= */
    /* == STYLE SWIPER (TIDAK BERUBAH) == */
    /* ================================================= */
    .swiper-container-wrapper {
        position: relative;
        padding: 0;
    }
    .swiper {
        overflow: hidden;
        padding-bottom: 1.5rem;
    }
    .swiper-slide {
        width: 100%;
        height: auto;
    }
    .swiper-button-next,
    .swiper-button-prev {
        position: absolute;
        top: 40%; /* Posisi panah di tengah gambar */
        transform: translateY(-50%);
        z-index: 10;
        color: white; /* Panah tetap putih */
        transition: color 0.2s ease;
        width: auto;
        height: auto;
        background-color: transparent;
        border-radius: 0;
        box-shadow: none;
    }
    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        color: #f0f0f0;
    }
    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5); /* Shadow agar terlihat */
    }
    .swiper-button-prev {
        left: 20px;
    }
    .swiper-button-next {
        right: 20px;
    }
    .swiper-button-disabled {
        opacity: 0;
        pointer-events: none;
    }

    /* ================================================= */
    /* == STYLE KAJIAN RUTIN (TIDAK BERUBAH) == */
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
    
    /* Ini adalah style dasar badge tanggal (dipakai oleh Kajian Rutin) */
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
</style>
@endpush

@section('content')
<div class="container pt-4 pb-2">
    <h2 class="donasi-title-heading">
        Kajian-Kajian
    </h2>
</div>
<div class="pt-3 pb-2">
    <div class="swiper-container-wrapper">
        <div class="swiper kajian-event-swiper">
            <div class="swiper-wrapper">

                @forelse($kajianEvent as $kajian)
                    <div class="swiper-slide">
                        
                        <div class="kajian-event-card">
                            
                            {{-- 1. FOTO --}}
                            <img src="{{ $kajian->foto_url ?? asset('images/events/hannan-attaki.jpeg') }}" alt="{{ $kajian->tema_kajian }}"
                                 class="kajian-event-img">
                            
                            {{-- 2. KONTEN --}}
                            <div class="kajian-event-content">
                                {{-- Badge "Event" oranye --}} 
                                <span class="badge mb-2">Event</span>
                                <h6 class="fw-bold">{{ $kajian->nama_penceramah }}</h6>
                                <p class="text-muted small mb-1">Tema: "{{ $kajian->tema_kajian }}"</p>
                                
                                {{-- Badge Tanggal (Warna barunya akan diambil dari CSS override) --}}
                                <span class="tanggal-badge">
                                    {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                                    @if($kajian->waktu_kajian)
                                        , Pukul {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="swiper-slide">
                        {{-- Card "empty" disesuaikan agar mirip --}}
                        <div class="kajian-event-card" style="height: 380px; display: flex; justify-content: center; align-items: center; background: #f8f9fa;">
                            <div class="text-center">
                                <img src="{{ asset('images/icons/kajian.png') }}" alt="Kajian" style="width: 80px; height: 80px; opacity: 0.5;" class="mx-auto mb-3">
                                <p class="text-muted">Belum ada kajian event terbaru.</p>
                            </div>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>
        {{-- Panah navigasi (tetap di luar) --}}
        <div class="swiper-button-prev event-button-prev"></div>
        <div class="swiper-button-next event-button-next"></div>
    </div>
</div>

{{-- Bagian Kajian Rutin (Tidak Diubah) --}}
<div class="feature-section-bg py-4">
    <div class="container">
        <h2 class="donasi-title-heading" style="font-size: 1.5rem; margin-bottom: 1rem;">Kajian Rutin</h2>
        
        @forelse($kajianRutin as $kajian)
            <div class="card kajian-list-card-new">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="{{ $kajian->foto_url ?? asset('images/default.png') }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <div>
                                <h5 class="card-title">{{ $kajian->nama_penceramah }}</h5>
                                <p class="card-text-tema">
                                    Tema: "{{ $kajian->tema_kajian }}"
                                </p>
                            </div>
                            
                            <div>
                                {{-- Badge Tanggal (Ini akan tetap memakai warna #343a40) --}}
                                <span class="tanggal-badge">
                                    {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                                    @if($kajian->waktu_kajian)
                                        , {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                Belum ada jadwal kajian rutin yang tersedia.
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.kajian-event-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: {{ $kajianEvent->count() > 1 ? 'true' : 'false' }}, // <-- Loop dinamis
            navigation: {
                nextEl: '.event-button-next',
                prevEl: '.event-button-prev',
            },
        });
    });
</script>
@endpush