@extends('layouts.public')

@section('title', 'Jadwal Kajian')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* Base styles (Mobile) */
    .donasi-title-heading {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .donasi-title-sub {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .kajian-event-card {
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: none;
        background-color: #ffffff;
    }
    .kajian-event-img {
        width: 100%;
        object-fit: cover;
        aspect-ratio: 16/9; /* Mobile aspect ratio */
    }
    .kajian-event-content {
        padding: 1rem;
    }
    .kajian-event-content h6 {
        font-weight: 700;
        font-size: 1.25rem;
        color: #212529;
        margin-bottom: 0.25rem;
    }
    .kajian-event-content p {
        font-size: 1.0rem;
        color: #495057;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    .kajian-event-content .text-muted,
    .kajian-event-content .small {
        color: #495057 !important;
        font-size: 1.0rem;
    }
    .kajian-event-content .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.4em 0.8em;
        background-color: #0d6efd !important;
        color: #ffffff !important;
        text-shadow: none;
        border-radius: 6px;
    }
    .tanggal-badge {
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.5em 0.75em;
        background-color: #343a40;
        color: #ffffff;
        border-radius: 8px;
        display: inline-block;
        margin-top: 0.5rem;
    }

    .kajian-list-card-new {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 1rem;
        overflow: hidden;
        background: linear-gradient(to bottom right, #ffffff, #f1f3f5);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: row;
    }
    .kajian-list-card-new:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .kajian-list-card-new .card-img {
        object-fit: cover;
        width: 110px; /* Lebar fix */
        height: 110px; /* Tinggi fix (1:1) */
        border-radius: 12px 0 0 12px !important;
    }
    
    /* =================================================
      PERUBAHAN: Menghapus height: 110px
      =================================================
    */
    .kajian-list-card-new .card-body {
        padding: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex: 1; /* Ambil sisa ruang */
        /* height: 110px; <-- DIHAPUS */
        min-height: 110px; /* Tambahkan min-height agar tetap rapi */
    }
    
    .kajian-list-card-new .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212529;
        line-height: 1.3;
    }
    .kajian-list-card-new .card-text-tema {
        font-size: 0.85rem;
        color: #495057;
    }


    /* Desktop layout (Breakpoint >= 992px) */
    @media (min-width: 992px) {
        
        /* KAJIAN EVENT (Left Column) */
        .kajian-event-swiper { height: 100%; }
        .swiper-wrapper { /* biarkan dinamis */ }
        .swiper-slide { height: auto; }
        .kajian-event-card { display: flex; }

        .kajian-event-img {
            aspect-ratio: 4 / 5; 
            width: 100%; 
            object-fit: cover;
            border-radius: 16px 0 0 16px !important; 
        }
        .kajian-event-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem; 
            height: 100%; 
        }
        .kajian-event-content .tanggal-badge {
            align-self: flex-start;
        }


        /* KAJIAN RUTIN (Right Column) */
        .kajian-list-card-new {
            background: #f1f3f5; 
            box-shadow: none;
            border-radius: 20px; 
            padding: 0.75rem;
            background-image: none; 
            display: flex;
            flex-direction: row;
        }
        .kajian-list-card-new:hover {
            transform: none; 
            box-shadow: none;
        }
        
        .kajian-list-card-new .card-img {
            width: 110px;
            height: 110px;
            border-radius: 12px !important; /* Kotak rounded */
            object-fit: cover;
            min-height: auto; 
        }
        
        .kajian-list-card-new .card-body {
            height: auto; 
            min-height: 0; /* Hapus min-height di desktop */
            padding: 0.5rem 0 0.5rem 1rem; 
            justify-content: space-between;
            flex: 1;
        }
        .kajian-list-card-new .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .kajian-list-card-new .card-text-tema {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }
        .kajian-list-card-new .tanggal-badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
            margin-top: 0.25rem;
        }

        /* Pagination Styles */
        .pagination .page-item .page-link {
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            margin: 0 2px;
        }
        .pagination .page-item.active .page-link {
            background-color: #0a58ca;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #e9ecef;
            color: #6c757d;
        }
    }
</style>
@endpush

@section('content')

<div class="container"> 

    {{-- Desktop Layout (>= 992px) --}}
    <div class="d-none d-lg-block" style="padding-top: 2rem; padding-bottom: 2rem;">

        {{-- Judul Halaman --}}
        <div class="mb-4">
            <h1 class="donasi-title-heading" style="font-size: 2.2rem; margin-bottom: 0;">Kajian-Kajian</h1>
            <p class="donasi-title-sub">Ikuti kajian event dan rutin terbaru kami.</p>
        </div>

        <div class="row">
            {{-- KIRI: KAJIAN EVENT --}}
            <div class="col-lg-7">
                <div class="swiper kajian-event-swiper">
                    <div class="swiper-wrapper">
                        @forelse($kajianEvent as $kajian)
                        <div class="swiper-slide">
                            <div class="kajian-event-card">
                                <div class="row g-0">
                                    <div class="col-md-6">
                                        <img src="{{ $kajian->foto_url ?? asset('images/events/default.jpg') }}" alt="{{ $kajian->tema_kajian }}" class="kajian-event-img">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="kajian-event-content">
                                            <h3 class="fw-bold donasi-title-heading" style="font-size: 1.5rem;">KAJIAN EVENT</h3>
                                            
                                            <h6 class="fw-bold mt-2">{{ $kajian->nama_penceramah }}</h6>
                                            <p class="text-muted small mb-1">Tema: "{{ $kajian->tema_kajian }}"</p>
                                            <span class="tanggal-badge">
                                                {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                                                @if($kajian->waktu_kajian), Pukul {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}@endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="swiper-slide">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <p>Tidak ada kajian event.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    @if($kajianEvent->count() > 1)
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    @endif
                </div>
            </div>

            {{-- KANAN: KAJIAN RUTIN --}}
            <div class="col-lg-5">
                <h2 class="donasi-title-heading mb-4">Kajian Rutin</h2>
                @forelse($kajianRutin as $kajian)
                <div class="card kajian-list-card-new">
                    <img src="{{ $kajian->foto_url ?? asset('images/default.png') }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">{{ $kajian->nama_penceramah }}</h5>
                            <p class="card-text-tema">Tema: "{{ $kajian->tema_kajian }}"</p>
                        </div>
                        <div>
                            <span class="tanggal-badge">
                                {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                                @if($kajian->waktu_kajian), {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}@endif
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <p>Tidak ada kajian rutin.</p>
                @endforelse

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-end mt-4">
                    @if(method_exists($kajianRutin, 'links'))
                        {{ $kajianRutin->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile & Tablet Layout (< 992px) --}}
    <div class="d-lg-none">
        
        <div class="pt-4 pb-3">
            <h2 class="donasi-title-heading">Kajian-Kajian</h2>
            <p class="donasi-title-sub">Ikuti kajian event dan rutin terbaru kami.</p>
        </div>

        <div class="swiper kajian-event-swiper-mobile mb-4">
            <div class="swiper-wrapper">
                @forelse($kajianEvent as $kajian)
                <div class="swiper-slide">
                    <div class="kajian-event-card">
                        <img src="{{ $kajian->foto_url ?? asset('images/events/default.jpg') }}" alt="{{ $kajian->tema_kajian }}" class="kajian-event-img">
                        <div class="kajian-event-content">
                            <h6 class="fw-bold">{{ $kajian->nama_penceramah }}</h6>
                            <p class="text-muted small mb-1">Tema: "{{ $kajian->tema_kajian }}"</p>
                            <span class="tanggal-badge">
                                {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                                @if($kajian->waktu_kajian), Pukul {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}@endif
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="swiper-slide">
                    <p>Tidak ada kajian event.</p>
                </div>
                @endforelse
            </div>
            @if($kajianEvent->count() > 1)
            <div class="swiper-pagination"></div>
            @endif
        </div>

        {{-- Judul "Kajian Rutin" untuk mobile/tablet --}}
        <h2 class="donasi-title-heading mt-4">Kajian Rutin</h2>
        
        @forelse($kajianRutin as $kajian)
        <div class="card kajian-list-card-new">
            <img src="{{ $kajian->foto_url ?? asset('images/default.png') }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
            <div class="card-body">
                <div>
                    <h5 class="card-title">{{ $kajian->nama_penceramah }}</h5>
                    <p class="card-text-tema">Tema: "{{ $kajian->tema_kajian }}"</p>
                </div>
                <div>
                    <span class="tanggal-badge">
                        {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                        @if($kajian->waktu_kajian), {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}@endif
                    </span>
                </div>
            </div>
        </div>
        @empty
        <p>Tidak ada kajian rutin.</p>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.innerWidth >= 992) {
            new Swiper('.kajian-event-swiper', {
                loop: {{ $kajianEvent->count() > 1 ? 'true' : 'false' }},
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoHeight: true, 
                observer: true,
                observeParents: true,
            });
        } else {
            new Swiper('.kajian-event-swiper-mobile', {
                loop: {{ $kajianEvent->count() > 1 ? 'true' : 'false' }},
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            });
        }
    });
</script>
@endpush