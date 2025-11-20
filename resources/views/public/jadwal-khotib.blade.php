@extends('layouts.public')

@section('title', 'Jadwal Khutbah Jumat')

@push('styles')
{{-- CSS INI DIAMBIL LANGSUNG DARI FILE KAJIAN ANDA & DI-RENAME --}}
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

    .khotib-card { /* (was kajian-event-card) */
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: none;
        background-color: #ffffff;
    }
    .khotib-img { /* (was kajian-event-img) */
        width: 100%;
        object-fit: cover;
        aspect-ratio: 16/9; /* Mobile aspect ratio */
    }
    .khotib-content { /* (was kajian-event-content) */
        padding: 1rem;
    }
    .khotib-content h6 {
        font-weight: 700;
        font-size: 1.25rem;
        color: #212529;
        margin-bottom: 0.25rem;
    }
    .khotib-content p {
        font-size: 1.0rem;
        color: #495057;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    .khotib-content .text-muted,
    .khotib-content .small {
        color: #495057 !important;
        font-size: 1.0rem;
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
    
    .khotib-list-card { /* (was kajian-list-card-new) */
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
    .khotib-list-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    .khotib-list-card .card-img {
        object-fit: cover;
        width: 110px; /* Lebar fix */
        height: 110px; /* Tinggi fix 1:1 */
        min-height: auto;
        border-radius: 12px 0 0 12px !important;
    }
    .khotib-list-card .card-body {
        padding: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Diubah agar badge di bawah */
        flex: 1; /* Ambil sisa ruang */
        min-height: 110px; /* Samakan min-height (bukan height) */
    }
    .khotib-list-card .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212529;
        line-height: 1.3;
    }
    .khotib-list-card .card-text-tema {
        font-size: 0.85rem;
        color: #495057;
    }


    /* Desktop layout (Breakpoint >= 992px) */
    @media (min-width: 992px) {
        
        /* KIRI: "JUMAT INI" */
        .khotib-card { display: flex; }
        .khotib-img {
            aspect-ratio: 4 / 5; 
            width: 100%; 
            object-fit: cover;
            border-radius: 16px 0 0 16px !important; 
        }
        .khotib-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem; 
            height: 100%; 
        }
        .khotib-content .tanggal-badge {
            align-self: flex-start;
        }

        /* KANAN: "KHUTBAH SELANJUTNYA" */
        .khotib-list-card {
            background: #f1f3f5; 
            box-shadow: none;
            border-radius: 20px; 
            padding: 0.75rem;
            background-image: none; 
        }
        .khotib-list-card:hover {
            transform: none; 
            box-shadow: none;
        }
        
        .khotib-list-card .card-img {
            width: 110px; 
            height: 110px;
            border-radius: 12px !important; /* Diubah dari 50% */
            object-fit: cover;
            min-height: auto; 
        }
        .khotib-list-card .card-body {
            padding: 0.5rem 0 0.5rem 1rem; 
            justify-content: space-between; /* Diubah dari center */
            height: auto;
            min-height: 0;
            flex: 1;
        }
        .khotib-list-card .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .khotib-list-card .card-text-tema {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }
        .khotib-list-card .tanggal-badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
            margin-top: 0.25rem;
        }

        /* Pagination Styles (jika nanti Anda perlu) */
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

    @php
        $khotibJumatIni = $jadwalKhotib->first();
        $khotibSelanjutnya = $jadwalKhotib->skip(1);
    @endphp

    {{-- Desktop Layout (>= 992px) --}}
    <div class="d-none d-lg-block" style="padding-top: 2rem; padding-bottom: 2rem;">

        {{-- Judul Halaman --}}
        <div class="mb-4">
            <h1 class="donasi-title-heading" style="font-size: 2.2rem; margin-bottom: 0;">Jadwal Khutbah Jumat</h1>
            <p class="donasi-title-sub">Lihat jadwal khotib dan imam untuk Jumat ini dan selanjutnya.</p>
        </div>

        <div class="row">
            {{-- KIRI: "JUMAT INI" --}}
            <div class="col-lg-7">
                @if($khotibJumatIni)
                <div class="khotib-card">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img src="{{ $khotibJumatIni->foto_url ?? asset('images/events/default.jpg') }}" alt="{{ $khotibJumatIni->nama_khotib }}" class="khotib-img">
                        </div>
                        <div class="col-md-6">
                            <div class="khotib-content">
                                <h3 class="fw-bold donasi-title-heading" style="font-size: 1.5rem;">KHUTBAH JUMAT INI</h3>
                                <h6 class="fw-bold mt-2">Khotib: {{ $khotibJumatIni->nama_khotib }}</h6>
                                <p class="text-muted small mb-1">Imam: {{ $khotibJumatIni->nama_imam }}</p>
                                <p class="text-muted small mb-1">Tema: "{{ $khotibJumatIni->tema_khutbah }}"</p>
                                <span class="tanggal-badge">
                                    {{ \Carbon\Carbon::parse($khotibJumatIni->tanggal)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="d-flex justify-content-center align-items-center h-100">
                    <p>Tidak ada jadwal khotib untuk Jumat ini.</p>
                </div>
                @endif
            </div>

            {{-- KANAN: "KHUTBAH SELANJUTNYA" --}}
            <div class="col-lg-5">
                <h2 class="donasi-title-heading mb-4">Khutbah Selanjutnya</h2>
                @forelse($khotibSelanjutnya as $khotib)
                <div class="card khotib-list-card">
                    <img src="{{ $khotib->foto_url ?? asset('images/default.png') }}" class="card-img" alt="Foto {{ $khotib->nama_khotib }}">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">Khotib: {{ $khotib->nama_khotib }}</h5>
                            <p class="card-text-tema">Imam: {{ $khotib->nama_imam }}</p>
                        </div>
                        <div>
                            <span class="tanggal-badge">
                                {{ \Carbon\Carbon::parse($khotib->tanggal)->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <p>Tidak ada jadwal khotib selanjutnya.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Mobile & Tablet Layout (< 992px) --}}
    <div class="d-lg-none">
        
        <div class="pt-4 pb-3">
            <h2 class="donasi-title-heading">Jadwal Khutbah Jumat</h2>
            <p class="donasi-title-sub">Lihat jadwal khotib dan imam untuk Jumat ini dan selanjutnya.</p>
        </div>

        {{-- =================================================
          PERUBAHAN 1: Menghapus H2 dari sini
          =================================================
        --}}
        {{-- <h2 class="donasi-title-heading">Khutbah Jumat Ini</h2> --}}
        
        @if($khotibJumatIni)
        <div class="khotib-card mb-4">
            <img src="{{ $khotibJumatIni->foto_url ?? asset('images/events/default.jpg') }}" alt="{{ $khotibJumatIni->nama_khotib }}" class="khotib-img">
            <div class="khotib-content">
                {{-- =================================================
                  PERUBAHAN 2: Menambahkan H3 di dalam card
                  =================================================
                --}}
                <h3 class="fw-bold donasi-title-heading" style="font-size: 1.5rem;">KHUTBAH JUMAT INI</h3>
                                
                {{-- =================================================
                  PERUBAHAN 3: Menambahkan margin-top
                  =================================================
                --}}
                <h6 class="fw-bold mt-2">Khotib: {{ $khotibJumatIni->nama_khotib }}</h6>
                <p class="text-muted small mb-1">Imam: {{ $khotibJumatIni->nama_imam }}</p>
                <p class="text-muted small mb-1">Tema: "{{ $khotibJumatIni->tema_khutbah }}"</p>
                <span class="tanggal-badge">
                    {{ \Carbon\Carbon::parse($khotibJumatIni->tanggal)->format('d M Y') }}
                </span>
            </div>
        </div>
        @else
        <p>Tidak ada jadwal khotib untuk Jumat ini.</p>
        @endif

        <h2 class="donasi-title-heading">Khutbah Selanjutnya</h2>
        @forelse($khotibSelanjutnya as $khotib)
        <div class="card khotib-list-card">
            <img src="{{ $khotib->foto_url ?? asset('images/default.png') }}" class="card-img" alt="Foto {{ $khotib->nama_khotib }}">
            <div class="card-body">
                <div>
                    <h5 class="card-title">Khotib: {{ $khotib->nama_khotib }}</h5>
                    <p class="card-text-tema">Imam: {{ $khotib->nama_imam }}</p>
                </div>
                <div>
                    <span class="tanggal-badge">
                        {{ \Carbon\Carbon::parse($khotib->tanggal)->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <p>Tidak ada jadwal khotib selanjutnya.</p>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
{{-- No scripts needed for this page --}}
@endpush