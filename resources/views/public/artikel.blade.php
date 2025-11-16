@extends('layouts.public')

@section('title', 'Artikel')

@push('styles')
{{-- Hapus <link> Swiper karena tidak dipakai --}}
<style>
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
    /* == STYLE ARTIKEL CARD (BARU) == */
    /* ================================================= */
    .artikel-card {
        width: 100%;
        border: none;
        border-radius: 12px; 
        
        /* 1. Background (Diganti jadi abu-abu lebih gelap) */
        /* background: linear-gradient(to bottom right, #ffffff, #f0f0f0); */
        background-color: #d1d6db; /* <-- PERUBAHAN BG CARD */
        
        /* 2. Shadow */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); 
        margin-bottom: 1.5rem; /* Jarak antar card */
        overflow: hidden; 

        /* 3. Animasi Hover */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .artikel-card:hover {
        /* Efek terangkat */
        transform: translateY(-5px); 
        /* Shadow lebih jelas */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12); 
    }

    /* 4. Gambar (Sekarang di dalam card-body) */
    .artikel-card-img {
        width: 100%;
        aspect-ratio: 14 / 9;
        object-fit: cover; 
        /* Gambar akan otomatis 'lebih kecil' karena ada padding dari .artikel-card-body */
    }
    
    .artikel-card-body {
        padding: 1rem; /* Padding dikurangi sedikit */
    }

    .artikel-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
    }
    .artikel-card-sinopsis {
        font-size: 1.0rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .artikel-card-date {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem; /* Jarak ke tombol */
    }

    /* 5. Tombol "Lihat Detail" */
    .btn-artikel {
        display: inline-block;
        font-weight: 600;
        font-size: 0.9rem;
        color: #ffffff;
        background-color: #0d6efd; /* Warna biru primer */
        border: none;
        border-radius: 50px; /* Bentuk pil */
        padding: 0.4rem 1.2rem;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
        .btn-artikel:hover {                                                                 
            background-color: #0b5ed7; /* Biru lebih gelap saat hover */                     
            color: #ffffff;                                                                  
        }
    
        @media (min-width: 768px) {
            .artikel-list-wrapper {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }
    
        @media (min-width: 992px) {
            .artikel-list-wrapper {
                grid-template-columns: repeat(3, 1fr);
            }
        }
                                                                                             
    </style>@endpush

@section('content')

<div class="py-4">
    <div class="container">
        
        {{-- Judul Halaman --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="donasi-title-heading">Artikel-Artikel</h2>
            </div>
        </div>

        <div class="artikel-list-wrapper">
            
            {{-- DUMMY ARTIKEL 1 --}}
            <div class="card artikel-card">
                {{-- <img src="{{ asset('images/artikel/artikel1.jpg') }}" class="card-img-top artikel-card-img" alt="Artikel 1"> --}} {{-- <-- FOTO DIPINDAH DARI SINI --}}
                <div class="card-body artikel-card-body">
                    <img src="{{ asset('images/artikel/artikel1.jpg') }}" class="card-img-top artikel-card-img mb-3" alt="Artikel 1"> {{-- <-- PERUBAHAN LOKASI FOTO + mb-3 --}}
                    <h5 class="artikel-card-title">Judul Artikel Menarik Pertama</h5>
                    <p class="artikel-card-sinopsis">Ini adalah sinopsis singkat untuk artikel pertama...</p>
                    <p class="artikel-card-date">07 November 2025</p>
                    <a href="#" class="btn-artikel">Lihat Detail</a>
                </div>
            </div>

            {{-- DUMMY ARTIKEL 2 --}}
            <div class="card artikel-card">
                <div class="card-body artikel-card-body">
                    <img src="{{ asset('images/artikel/artikel2.jpg') }}" class="card-img-top artikel-card-img mb-3" alt="Artikel 2"> {{-- <-- PERUBAHAN LOKASI FOTO + mb-3 --}}
                    <h5 class="artikel-card-title">Judul Artikel Populer Kedua</h5>
                    <p class="artikel-card-sinopsis">Ini adalah sinopsis singkat untuk artikel kedua...</p>
                    <p class="artikel-card-date">06 November 2025</p>
                    <a href="#" class="btn-artikel">Lihat Detail</a>
                </div>
            </div>

            {{-- DUMMY ARTIKEL 3 --}}
            <div class="card artikel-card">
                <div class="card-body artikel-card-body">
                    <img src="{{ asset('images/artikel/artikel3.jpg') }}" class="card-img-top artikel-card-img mb-3" alt="Artikel 3"> {{-- <-- PERUBAHAN LOKASI FOTO + mb-3 --}}
                    <h5 class="artikel-card-title">Tips Bermanfaat di Artikel Ketiga</h5>
                    <p class="artikel-card-sinopsis">Ini adalah sinopsis singkat untuk artikel ketiga...</p>
                    <p class="artikel-card-date">05 November 2025</p>
                    <a href="#" class="btn-artikel">Lihat Detail</a>
                </div>
            </div>

            {{-- @forelse($artikel as $item)
            <div class="card artikel-card">
                <div class="card-body artikel-card-body">
                    <img src="{{ $item->foto_url ?? asset('images/default-artikel.jpg') }}" class="card-img-top artikel-card-img mb-3" alt="{{ $item->judul }}">
                    <h5 class="artikel-card-title">{{ $item->judul }}</h5>
                    <p class="artikel-card-sinopsis">{{ $item->sinopsis }}</p>
                    <p class="artikel-card-date">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</p>
                    <a href="{{ route('artikel.show', $item->slug) }}" class="btn-artikel">Lihat Detail</a>
                </div>
            </div>
            @empty
            <div class="alert alert-info text-center">
                Belum ada artikel yang dipublikasikan.
            </div>
            @endforelse --}}

        </div>
    </div>
</div>
@endsection
