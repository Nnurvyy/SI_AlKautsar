@extends('layouts.public')

@section('title', 'Donasi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* --- Style CSS tetap sama seperti sebelumnya --- */
    .swiper-container-wrapper { position: relative; padding: 0; }
    .swiper { overflow: hidden; padding-bottom: 1.5rem; }
    .swiper-slide { width: 100%; height: auto; }
    .swiper-button-next, .swiper-button-prev { position: absolute; top: 40%; transform: translateY(-50%); z-index: 10; color: white; }
    .swiper-button-prev { left: 20px; } .swiper-button-next { right: 20px; }
    
    /* Style Card */
    .donation-list-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); margin-bottom: 1rem; overflow: hidden; transition: transform 0.3s ease; }
    .donation-list-card:hover { transform: translateY(-5px); }
    .donation-list-card .card-img { object-fit: cover; height: 100%; min-height: 180px; border-radius: 12px 0 0 12px !important; }
    .donation-list-card .card-body { padding: 1rem; display: flex; flex-direction: column; justify-content: space-between; }
    .donation-list-card .card-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; }
    .donation-list-card .progress { height: 6px; border-radius: 6px; margin-top: 0.5rem; }
    .donation-list-card .progress-bar { background-color: #1abc9c; }
    .btn-donasi-small { background-color: #1abc9c; color: white; border-radius: 50px; padding: 0.4rem 1rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; }
    .btn-donasi-small:hover { background-color: #16a085; color: white; }

    /* Slider Card Style */
    .donation-slide { position: relative; height: 350px; width: 100%; border-radius: 16px; overflow: hidden; }
    .donation-slide-img { width: 100%; height: 100%; object-fit: cover; }
    .donation-slide-overlay { position: absolute; bottom: 0; left: 0; width: 100%; height: 60%; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 20px; display: flex; flex-direction: column; justify-content: flex-end; color: white; }

    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; }

    @media (min-width: 768px) { .donation-list-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; } }
    @media (min-width: 992px) { .donation-list-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')

{{-- 1. Header --}}
<div class="container pt-4 pb-3">
    <h2 class="donasi-title-heading">Mari Berdonasi</h2>
    <p class="donasi-title-sub">Setiap donasi Anda membawa harapan baru.</p>
</div>

{{-- 2. Slider (Featured / Semua Program) --}}
<div class="container mb-5">
    @if($programDonasi->isEmpty())
        <div class="alert alert-info text-center">Belum ada program donasi yang aktif saat ini.</div>
    @else
        <div class="swiper-container-wrapper">
            <div class="swiper donasi-swiper">
                <div class="swiper-wrapper">
                    @foreach($programDonasi as $program)
                    <div class="swiper-slide">
                        <div class="donation-slide">
                            <img src="{{ $program->gambar_url }}" class="donation-slide-img" alt="{{ $program->nama_donasi }}">
                            <div class="donation-slide-overlay">
                                <h5 class="fw-bold">{{ $program->nama_donasi }}</h5>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Terkumpul: Rp {{ number_format($program->dana_terkumpul, 0, ',', '.') }}</span>
                                    <span>{{ $program->persentase_asli }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 5px; background: rgba(255,255,255,0.3);">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $program->persentase }}%"></div>
                                </div>
                                {{-- Link ke detail atau modal (opsional) --}}
                                <a href="#" class="btn btn-light btn-sm w-100 rounded-pill fw-bold text-success">Donasi Sekarang</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @if($programDonasi->count() > 1)
                <div class="swiper-button-prev donasi-button-prev"></div>
                <div class="swiper-button-next donasi-button-next"></div>
            @endif
        </div>
    @endif
</div>

{{-- 3. List Grid (Daftar Program) --}}
<div class="container mt-3"> 
    <h4 class="fw-bold mb-4 text-dark">Daftar Donasi</h4>
    <div class="donation-list-grid">
        @foreach($programDonasi as $program)
        <div class="card donation-list-card">
            <div class="row g-0 h-100">
                <div class="col-4">
                    <img src="{{ $program->gambar_url }}" class="card-img" alt="{{ $program->nama_donasi }}">
                </div>
                <div class="col-8">
                    <div class="card-body h-100">
                        <div>
                            <h5 class="card-title text-dark">{{ $program->nama_donasi }}</h5>
                            <p class="small text-muted mb-2" style="line-height: 1.2;">
                                {{ Str::limit($program->deskripsi, 60) }}
                            </p>
                        </div>
                        
                        <div>
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="small text-muted">Terkumpul</span>
                                <span class="fw-bold text-success small">Rp {{ number_format($program->dana_terkumpul, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: {{ $program->persentase }}%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-danger fw-semibold">
                                    @if($program->tanggal_selesai)
                                        <i class="bi bi-clock"></i> Sisa {{ \Carbon\Carbon::parse($program->tanggal_selesai)->diffInDays(\Carbon\Carbon::now()) }} hari
                                    @else
                                        <i class="bi bi-infinity"></i> Unlimited
                                    @endif
                                </span>
                                <a href="#" class="btn-donasi-small">Donasi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.donasi-swiper')) {
            new Swiper('.donasi-swiper', { 
                slidesPerView: 1, 
                spaceBetween: 15, 
                loop: {{ $programDonasi->count() > 1 ? 'true' : 'false' }}, 
                navigation: { 
                    nextEl: '.donasi-button-next', 
                    prevEl: '.donasi-button-prev', 
                },
                breakpoints: {
                    768: { slidesPerView: 2, spaceBetween: 20 },
                    992: { slidesPerView: 3, spaceBetween: 25 }
                }
            }); 
        }
    });
</script>
@endpush