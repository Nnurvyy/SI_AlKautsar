@extends('layouts.public')

@section('title', 'Program Masjid')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* ================================================= */
    /* == STYLE JUDUL == */
    /* ================================================= */
    .program-title-heading {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .program-title-sub {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    /* ================================================= */
    /* == STYLE SWIPER == */
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
        top: 40%;
        transform: translateY(-50%);
        z-index: 10;
        color: white;
    }
    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
    }
    .swiper-button-prev { left: 20px; }
    .swiper-button-next { right: 20px; }
    .swiper-button-disabled { opacity: 0; pointer-events: none; }

    /* ================================================= */
    /* == STYLE SLIDE PROGRAM == */
    /* ================================================= */
    .program-slide {
        position: relative;
        height: 320px;
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        color: white;
    }
    .program-slide-img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        z-index: 0;
    }
    .program-slide-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.2));
        z-index: 1;
    }
    .program-slide-content {
        position: relative;
        z-index: 2;
        height: 100%;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .program-slide-badge {
        display: inline-block;
        padding: 0.3em 0.8em;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 50px;
        background-color: rgba(255, 255, 255, 0.2); 
        color: white;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(4px);
    }
    .program-slide-content h5 {
        font-weight: 700;
        font-size: 1.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    .program-slide-content p {
        font-size: 1.0rem;
        margin-bottom: 0;
        line-height: 1.5;
        font-weight: 500;
        color: #f8f9fa;
    }

    /* ================================================= */
    /* == STYLE CARD LIST PROGRAM == */
    /* ================================================= */
    .program-list-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
        height: 100%;
    }
    .program-list-card:hover {
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); 
    }
    .program-list-card .card-img {
        object-fit: cover;
        height: 100%; 
        min-height: 160px; 
        width: 100%;
    }
    .program-list-card .card-body {
        padding: 1rem; 
        display: flex;
        flex-direction: column;
        justify-content: space-between; 
        height: 100%;
    }
    .program-list-card .card-title {
        font-size: 1rem; 
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        /* Batasi 2 baris */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;   
        overflow: hidden;
    }
    .program-list-card .card-badge {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    .program-list-card .card-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }
    .program-list-card .card-location {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    .program-list-card .btn-detail-small {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        background-color: #0d6efd; 
        border: none;
        color: white;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-block;
    }
    .program-list-card .btn-detail-small:hover { 
        background-color: #0b5ed7; 
    }
    
    /* Helper colors for badges */
    .text-success-custom { color: #27ae60; }
    .text-warning-custom { color: #f39c12; }
    .text-secondary-custom { color: #7f8c8d; }
    
    @media (min-width: 768px) {
        .program-list-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        /* Fix image styling in grid for horizontal card look */
        .program-list-card .row.g-0 { height: 100%; }
        .program-list-card .col-4 { height: 100%; }
        .program-list-card .card-img {
            border-radius: 12px 0 0 12px !important;
            height: 100%;
        }
    }
    
    @media (min-width: 992px) {
        .program-list-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
                                                            
    </style>
@endpush

@section('content')

{{-- 1. JUDUL HALAMAN --}}
<div class="container pt-4 pb-3">
    <h2 class="program-title-heading">
        Program & Workshop
    </h2>
    <p class="program-title-sub">
        Ikuti kegiatan dan workshop terbaru dari kami untuk meningkatkan keimanan dan wawasan.
    </p>
</div>

{{-- 2. SLIDER PROGRAM (Hanya Tampil jika ada program mendatang) --}}
@if($sliderPrograms->count() > 0)
<div class="container mb-5">
    <div class="swiper-container-wrapper">
        <div class="swiper program-swiper">
            <div class="swiper-wrapper">

                @foreach($sliderPrograms as $item)
                <div class="swiper-slide">
                    <div class="program-slide">
                        {{-- Gambar Background --}}
                        <img src="{{ $item->foto_url }}" alt="{{ $item->nama_program }}" class="program-slide-img">
                        
                        <div class="program-slide-overlay"></div>
                        
                        <div class="program-slide-content">
                            <span class="program-slide-badge">Akan Datang</span>
                            <h5>{{ $item->nama_program }}</h5>
                            <p>
                                <i class="bi bi-calendar-event me-1"></i> 
                                {{ $item->tanggal_program->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
        <div class="swiper-button-prev program-button-prev"></div>
        <div class="swiper-button-next program-button-next"></div>
    </div>
</div>
@endif


{{-- 3. DAFTAR CARD SEMUA PROGRAM --}} 
<div class="container mt-3 mb-5"> 
    
    {{-- Cek apakah ada data --}}
    @if($semuaProgram->isEmpty())
        <div class="alert alert-info text-center py-5 rounded-3 shadow-sm">
            <i class="bi bi-info-circle fs-1 mb-3 d-block text-info"></i>
            <h5 class="fw-bold">Belum ada Program</h5>
            <p class="mb-0">Saat ini belum ada data program atau kegiatan yang tersedia.</p>
        </div>
    @else
        
        <div class="program-list-grid">
            @foreach($semuaProgram as $program)
            
            {{-- Tentukan Warna Badge berdasarkan Status --}}
            @php
                $badgeColor = match($program->status_program) {
                    'belum dilaksanakan' => 'text-success-custom', // Hijau
                    'sedang berjalan' => 'text-warning-custom',    // Kuning/Orange
                    'sudah dijalankan' => 'text-secondary-custom', // Abu-abu
                    default => 'text-primary'
                };
                
                $statusLabel = ucwords($program->status_program ?? 'Program');
            @endphp

            <div class="card program-list-card"> 
                <div class="row g-0 h-100">
                    <div class="col-4">
                        <img src="{{ $program->foto_url }}" class="card-img" alt="{{ $program->nama_program }}">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            {{-- Bagian Atas: Info --}}
                            <div> 
                                <div class="card-badge {{ $badgeColor }}">
                                    {{ $statusLabel }}
                                </div>
                                
                                <h5 class="card-title">{{ $program->nama_program }}</h5>
                                
                                <p class="card-date">
                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                    {{ $program->tanggal_program->translatedFormat('d M Y') }}
                                </p>
                                
                                <p class="card-location text-truncate">
                                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                                    {{ $program->lokasi_program }}
                                </p>
                            </div>
                            
                            {{-- Bagian Bawah: Tombol (Bisa diarahkan ke detail jika ada route-nya) --}}
                            <div class="text-end">
                                <button type="button" class="btn-detail-small" onclick="alert('Detail: {{ $program->nama_program }}')">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div> 

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $semuaProgram->links() }}
        </div>
    @endif
    
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hanya inisialisasi Swiper jika elemen ada
        if(document.querySelector('.program-swiper')) {
            new Swiper('.program-swiper', { 
                slidesPerView: 1, 
                spaceBetween: 15, 
                loop: true, 
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                navigation: { 
                    nextEl: '.program-button-next', 
                    prevEl: '.program-button-prev', 
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    992: {
                        slidesPerView: 2.5,
                        centeredSlides: true,
                        spaceBetween: 30
                    },
                    1200: {
                        slidesPerView: 3,
                        centeredSlides: true,
                        spaceBetween: 30
                    }
                }
            });     
        }
    });
</script>
@endpush