@extends('layouts.public')

@section('title', 'Program Masjid')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* ================================================= */
    /* == STYLE JUDUL (Seperti Donasi) == */
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
    /* == STYLE SWIPER (Umum, dari Donasi) == */
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
    /* == STYLE SLIDE PROGRAM (Diadaptasi dari Donasi) == */
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
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.3));
        z-index: 1;
    }
    .program-slide-content {
        position: relative;
        z-index: 2;
        height: 100%;
        padding: 1rem;
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
    }
    .program-slide-content h5 {
        font-weight: 700;
        font-size: 1.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    .program-slide-content p {
        font-size: 1.0rem;
        margin-bottom: 0;
        line-height: 1.5;
        font-weight: 500;
    }

    /* ================================================= */
    /* == STYLE CARD LIST PROGRAM (BARU) == */
    /* ================================================= */
    .program-list-card {
        border: none;
        border-radius: 12px; /* Tepi tumpul */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Shadow halus */
        margin-bottom: 1rem;
        overflow: hidden; /* Penting untuk rounded corners */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .program-list-card:hover {
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12); 
    }
    .program-list-card .card-img {
        object-fit: cover;
        height: 100%; /* Gambar mengisi tinggi kolomnya */
        min-height: 140px; /* Tinggi minimal */
        border-radius: 12px 0 0 12px !important; /* Tumpul di kiri */
    }
    .program-list-card .card-body {
        padding: 0.75rem 1rem; /* Padding lebih kecil */
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* <- Kunci layout */
        min-height: 140px; /* Samakan dgn tinggi min gambar */
    }
    .program-list-card .card-title {
        font-size: 0.95rem; /* Judul sedikit lebih kecil */
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 0.25rem;
        /* Batasi 2 baris */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
    /* Style untuk Badge di card list */
    .program-list-card .card-badge {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    /* Style untuk Tanggal di card list */
    .program-list-card .card-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
    }
    /* Style untuk tombol "Lihat Detail" */
    .program-list-card .btn-detail-small {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        background-color: #0d6efd; /* Biru primer */
        border-color: #0d6efd;
        color: white;
        text-decoration: none;
    }
    .program-list-card .btn-detail-small:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
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
        Ikuti kegiatan dan workshop terbaru dari kami.
    </p>
</div>

{{-- 2. SWIPER --}}
<div class="py-0">
    <div class="swiper-container-wrapper">
        <div class="swiper program-swiper">
            <div class="swiper-wrapper">

                {{-- SLIDE 1 (STRUKTUR BARU) --}}
                <div class="swiper-slide">
                    <div class="program-slide">
                        <img src="https://via.placeholder.com/800x600/27ae60/ffffff?text=Workshop+1" alt="Workshop 1"
                            class="program-slide-img">
                        <div class="program-slide-overlay"></div>
                        <div class="program-slide-content">
                            <span class="program-slide-badge">Workshop</span>
                            <h5>Workshop Manajemen Masjid Digital</h5>
                            <p>15 November 2025</p>
                        </div>
                    </div>
                </div>

                {{-- SLIDE 2 (STRUKTUR BARU) --}}
                <div class="swiper-slide">
                    <div class="program-slide">
                        <img src="https://via.placeholder.com/800x600/2980b9/ffffff?text=Program+2" alt="Program 2"
                            class="program-slide-img">
                        <div class="program-slide-overlay"></div>
                        <div class="program-slide-content">
                            <span class="program-slide-badge">Program</span>
                            <h5>Pelatihan Pengurusan Jenazah</h5>
                            <p>22 November 2025</p>
                        </div>
                    </div>
                </div>

                {{-- SLIDE 3 (Contoh Tambahan) --}}
                <div class="swiper-slide">
                    <div class="program-slide">
                        <img src="https://via.placeholder.com/800x600/c0392b/ffffff?text=Workshop+3" alt="Workshop 3"
                            class="program-slide-img">
                        <div class="program-slide-overlay"></div>
                        <div class="program-slide-content">
                            <span class="program-slide-badge">Workshop</span>
                            <h5>Kiat Menjadi Imam & Muadzin</h5>
                            <p>29 November 2025</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="swiper-button-prev program-button-prev"></div>
        <div class="swiper-button-next program-button-next"></div>
    </div>
</div>


{{-- 3. DAFTAR CARD PROGRAM (BARU DITAMBAHKAN) --}}
<div class="container mt-3">
    
    {{-- Nanti Anda bisa loop @forelse($semuaProgram as $program) di sini --}}

    {{-- CARD 1 --}}
    <div class="card program-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="https://via.placeholder.com/800x600/27ae60/ffffff?text=Workshop+1" class="card-img" alt="Workshop 1">
            </div>
            <div class="col-8">
                <div class="card-body">
                    {{-- Bagian Atas: Info --}}
                    <div> 
                        <div class="card-badge" style="color: #27ae60;">WORKSHOP</div>
                        <h5 class="card-title">Workshop Manajemen Masjid Digital</h5>
                        <p class="card-date">
                            <i class="bi bi-calendar-event me-1"></i>15 November 2025
                        </p>
                    </div>
                    {{-- Bagian Bawah: Tombol --}}
                    <div class="text-end">
                        <a href="#" class="btn-detail-small">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 2 --}}
    <div class="card program-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="https://via.placeholder.com/800x600/2980b9/ffffff?text=Program+2" class="card-img" alt="Program 2">
            </div>
            <div class="col-8">
                <div class="card-body">
                    {{-- Bagian Atas: Info --}}
                    <div>
                        <div class="card-badge" style="color: #2980b9;">PROGRAM</div>
                        <h5 class="card-title">Pelatihan Pengurusan Jenazah</h5>
                        <p class="card-date">
                            <i class="bi bi-calendar-event me-1"></i>22 November 2025
                        </p>
                    </div>
                    {{-- Bagian Bawah: Tombol --}}
                    <div class="text-end">
                        <a href="#" class="btn-detail-small">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

     {{-- CARD 3 --}}
    <div class="card program-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="https://via.placeholder.com/800x600/c0392b/ffffff?text=Workshop+3" class="card-img" alt="Workshop 3">
            </div>
            <div class="col-8">
                <div class="card-body">
                    {{-- Bagian Atas: Info --}}
                    <div>
                        <div class="card-badge" style="color: #c0392b;">WORKSHOP</div>
                        <h5 class="card-title">Kiat Menjadi Imam & Muadzin</h5>
                        <p class="card-date">
                            <i class="bi bi-calendar-event me-1"></i>29 November 2025
                        </p>
                    </div>
                    {{-- Bagian Bawah: Tombol --}}
                    <div class="text-end">
                        <a href="#" class="btn-detail-small">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- @forelse($semuaProgram as $program)
    <div class="card program-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="{{ $program->foto_url ?? 'https://via.placeholder.com/800x600/ccc/ffffff?text=Program' }}" class="card-img" alt="{{ $program->nama }}">
            </div>
            <div class="col-8">
                <div class="card-body">
                    <div> 
                        <div class="card-badge" style="color: {{ $program->kategori == 'Workshop' ? '#27ae60' : '#2980b9' }};">{{ $program->kategori }}</div>
                        <h5 class="card-title">{{ $program->nama }}</h5>
                        <p class="card-date">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ \Carbon\Carbon::parse($program->tanggal_mulai)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('program.show', $program->slug) }}" class="btn-detail-small">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info text-center">
        Belum ada program atau workshop yang tersedia saat ini.
    </div>
    @endforelse --}}

</div>
@endsection

@push('scripts')
{{-- Script tidak perlu diubah --}}
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.program-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true, 
            navigation: {
                nextEl: '.program-button-next',
                prevEl: '.program-button-prev',
            },
        });
    });
</script>
@endpush