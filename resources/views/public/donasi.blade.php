@extends('layouts.public')

@section('title', 'Donasi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* Style untuk Swiper (KODE ASLI ANDA) */
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
        width: auto;
        height: auto;
        background-color: transparent;
        border-radius: 0;
        box-shadow: none;
        color: white;
        transition: color 0.2s ease;
    }
    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        color: #f0f0f0;
    }
    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
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

    /* Style untuk Slide Donasi (KODE ASLI ANDA) */
    .donation-slide {
        position: relative;
        height: 320px;
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        color: white;
    }
    .donation-slide-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }
    .donation-slide-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.3));
        z-index: 1;
    }
    .donation-slide-content {
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
    .donation-slide-content h5 {
        font-weight: 700;
        font-size: 1.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 0.75rem;
    }
    .donation-slide-content p {
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        line-height: 1.5;
        max-width: 90%;
    }
    .donation-slide-content .btn {
        background-color: #1abc9c;
        border-color: #1abc9c;
        color: white;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .donation-slide-content .btn:hover {
        background-color: #16a085;
        border-color: #16a085;
        color: white;
    }

    /* ================================== */
    /* */
    /* ================================== */
    .donation-list-card {
        border: none;
        border-radius: 12px; /* Tepi tumpul */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Shadow halus */
        margin-bottom: 1rem;
        overflow: hidden; /* Penting untuk rounded corners */

        /* === TAMBAHAN UNTUK ANIMASI HOVER === */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* === TAMBAHAN UNTUK ANIMASI HOVER === */
    .donation-list-card:hover {
        /* Efek card terangkat */
        transform: translateY(-5px); 
        
        /* Shadow menjadi lebih jelas saat terangkat */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12); 
    }
    .donation-list-card .card-img {
        object-fit: cover;
        height: 100%; /* Gambar mengisi tinggi kolomnya */
        min-height: 140px; /* Tinggi minimal jika teksnya pendek */
        border-radius: 12px 0 0 12px !important; /* Tumpul di kiri */
    }
    .donation-list-card .card-body {
        padding: 0.75rem 1rem; /* Padding lebih kecil */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .donation-list-card .card-title {
        font-size: 0.95rem; /* Judul sedikit lebih kecil */
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 0.5rem;
        /* Batasi 2 baris */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
    .donation-list-card .progress-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .donation-list-card .progress-amount {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1abc9c; /* Tema hijau */
        margin-bottom: 0.5rem;
    }
    .donation-list-card .progress {
        height: 6px; /* Progress bar tipis */
        border-radius: 6px;
    }
    .donation-list-card .progress-bar {
        background-color: #1abc9c;
    }
    .donation-list-card .progress-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.75rem;
    }
    .donation-list-card .days-left {
        font-size: 0.8rem;
        font-weight: 600;
        color: #e74c3c; /* Merah */
    }
    .donation-list-card .days-left span {
        color: #6c757d;
        font-weight: 500;
    }
    .donation-list-card .btn-donasi-small {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        background-color: #1abc9c;
        border-color: #1abc9c;
        color: white;
    }
    .donation-list-card .btn-donasi-small:hover {
        background-color: #16a085;
        border-color: #16a085;
    }


    .donasi-title-heading {
        font-family: 'Poppins', sans-serif;
        font-weight: 700; /* Bold */
        font-size: 1.8rem; /* Bigger */
        color: #333; /* Dark color */
        margin-bottom: 0.5rem;
    }
    
    .donasi-title-heading .bi {
        color: #1abc9c; /* Theme color */
        font-size: 1.5rem; /* Icon size */
        vertical-align: -2px; /* Align icon nicely */
    }
    
    .donasi-title-sub {
        font-size: 1rem;
        color: #6c757d; /* Muted text */
        margin-bottom: 1rem;
    }

</style>
@endpush

@section('content')

<div class="container pt-4 pb-3">
    <h2 class="donasi-title-heading">
        Mari Berdonasi
    </h2>
    <p class="donasi-title-sub">
        Setiap donasi Anda membawa harapan baru.
    </p>
</div>

    <div class="swiper-container-wrapper">
        <div class="swiper donasi-swiper">
            <div class="swiper-wrapper">

                {{-- Nanti Anda bisa loop @forelse($programDonasi as $donasi) di sini --}}

                <div class="swiper-slide">
                    <div class="donation-slide">
                        <img src="{{ asset('images/donasi/pembangunan-masjid.jpg') }}" alt="Pembangunan Masjid"
                            class="donation-slide-img">
                        <div class="donation-slide-overlay"></div>
                        <div class="donation-slide-content">
                            <h5>PEMBANGUNAN MASJID</h5>
                            <p>Bantu perluasan dan renovasi masjid untuk kenyamanan ibadah jamaah.</p>
                            <a href="#" class="btn">
                                <i class="bi bi-wallet2 me-2"></i>Donasi Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="donation-slide">
                        <img src="{{ asset('images/donasi/yatim.jpeg') }}" alt="Yatim & Dhuafa"
                            class="donation-slide-img">
                        <div class="donation-slide-overlay"></div>
                        <div class="donation-slide-content">
                            <h5>YATIM & DHUAFA</h5>
                            <p>Salurkan sedekah Anda untuk program santunan anak yatim dan dhuafa di sekitar masjid.</p>
                            <a href="#" class="btn">
                                <i class="bi bi-wallet2 me-2"></i>Donasi Sekarang
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="donation-slide">
                        <img src="https://via.placeholder.com/800x600/4682B4/ffffff?text=Operasional+Masjid" class="donation-slide-img" alt="Operasional Masjid">
                        <div class="donation-slide-overlay"></div>
                        <div class="donation-slide-content">
                            <h5>OPERASIONAL MASJID</h5>
                            <p>Dukung kegiatan dakwah dan biaya operasional masjid agar tetap makmur.</p>
                            <a href="#" class="btn">
                                <i class="bi bi-wallet2 me-2"></i>Donasi Sekarang
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            </div>
        <div class="swiper-button-prev donasi-button-prev"></div>
        <div class="swiper-button-next donasi-button-next"></div>
    </div>

<div class="container mt-3">
    
    <div class="card donation-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="{{ asset('images/donasi/pembangunan-masjid.jpg') }}" class="card-img" alt="Pembangunan Masjid">
            </div>
            <div class="col-8">
                <div class="card-body">
                    <h5 class="card-title">PEMBANGUNAN MASJID</h5>
                    <div>
                        <div class="progress-label">Terkumpul</div>
                        <div class="progress-amount">Rp 75.432.123</div>
                        <div class="progress" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 50%"></div>
                        </div>
                        <div class="progress-footer">
                            <div class="days-left"><span>Sisa:</span> 90 hari</div>
                            <a href="#" class="btn btn-donasi-small">Donasi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card donation-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="{{ asset('images/donasi/yatim.jpeg') }}" class="card-img" alt="Yatim & Dhuafa">
            </div>
            <div class="col-8">
                <div class="card-body">
                    <h5 class="card-title">YATIM, FAKIR MISKIN & DHUAFA</h5>
                    <div>
                        <div class="progress-label">Terkumpul</div>
                        <div class="progress-amount">Rp 25.123.456</div>
                        <div class="progress" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 62%"></div>
                        </div>
                        <div class="progress-footer">
                            <div class="days-left"><span>Sisa:</span> 30 hari</div>
                            <a href="#" class="btn btn-donasi-small">Donasi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card donation-list-card">
        <div class="row g-0">
            <div class="col-4">
                <img src="https://via.placeholder.com/800x600/4682B4/ffffff?text=Operasional+Masjid" class="card-img" alt="Operasional Masjid">
            </div>
            <div class="col-8">
                <div class="card-body">
                    <h5 class="card-title">OPERASIONAL MASJID</h5>
                    <div>
                        <div class="progress-label">Terkumpul</div>
                        <div class="progress-amount">Rp 5.000.000</div>
                        <div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 25%"></div>
                        </div>
                        <div class="progress-footer">
                            <div class="days-left"><span>Sisa:</span> 45 hari</div>
                            <a href="#" class="btn btn-donasi-small">Donasi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.donasi-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true, // Loop sekarang akan berfungsi karena ada > 1 slide
            navigation: {
                nextEl: '.donasi-button-next',
                prevEl: '.donasi-button-prev',
            },
        });
    });
</script>
@endpush