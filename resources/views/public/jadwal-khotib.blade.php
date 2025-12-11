@extends('layouts.public')

@section('title', 'Jadwal Khutbah Jumat')

@push('styles')
    <style>
        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }

        .page-link {
            border-radius: 8px !important;
            border: 1px solid #dee2e6;
            color: #333;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.2);
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
        }

        .page-link:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .khotib-title-heading {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .khotib-title-sub {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .khotib-card {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
            background-color: #ffffff;
            transition: transform 0.3s ease;
        }

        .khotib-img {
            width: 100%;
            object-fit: cover;
            aspect-ratio: 1/1;
        }

        .khotib-content {
            padding: 1.5rem;
        }

        .khotib-content h3 {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #2c3e50;
        }

        .tanggal-badge {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.5em 1em;
            background-color: #212529;
            color: #ffffff;
            border-radius: 50px;
            display: inline-block;
            margin-top: 1rem;
        }

        .khotib-list-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
            overflow: hidden;
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .khotib-list-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .khotib-list-card .card-img {
            object-fit: cover;
            width: 100px;
            height: 100px;
            aspect-ratio: 1/1;
            border-radius: 12px !important;
            margin: 0.5rem;
        }

        .khotib-list-card .card-body {
            padding: 0.75rem 1rem 0.75rem 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (min-width: 992px) {
            .khotib-card {
                display: flex;
                flex-direction: row;
                height: auto;
                align-items: stretch;
            }

            .khotib-img {
                aspect-ratio: 1/1;
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 0 !important;
            }

            .khotib-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 3rem;
                width: 100%;
                height: 100%;
            }

            .khotib-list-card {
                background: #f8f9fa;
                border: 1px solid #eee;
                box-shadow: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container pt-4 pb-5">

        {{-- Judul Halaman --}}
        <div class="mb-5 text-center text-lg-start">
            <h1 class="khotib-title-heading">Jadwal Khutbah Jumat</h1>
            <p class="khotib-title-sub">Informasi petugas khotib dan imam shalat Jumat di masjid kami.</p>
        </div>

        <div class="row g-5 align-items-start">

            {{-- KOLOM KIRI: Highlight Jumat Ini --}}
            <div class="col-lg-7">

                @if ($khotibJumatIni)
                    <div class="khotib-card">
                        <div class="row g-0 w-100 h-100">

                            {{-- Kolom Gambar (7 Bagian) --}}
                            <div class="col-md-7 p-0">
                                <img src="{{ $khotibJumatIni->foto_url }}" alt="{{ $khotibJumatIni->nama_khotib }}"
                                    class="khotib-img">
                            </div>

                            {{-- Kolom Teks (5 Bagian) --}}
                            <div class="col-md-5">
                                <div class="khotib-content h-100">
                                    <div class="badge bg-primary w-auto align-self-start mb-2">JUMAT TERDEKAT</div>

                                    <h3 class="mb-3">Jumat Ini</h3>

                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase ls-1">Khotib</small>
                                        <h5 class="fw-bold text-dark mt-1">{{ $khotibJumatIni->nama_khotib }}</h5>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase ls-1">Imam</small>
                                        <h5 class="fw-bold text-dark mt-1">{{ $khotibJumatIni->nama_imam }}</h5>
                                    </div>

                                    <div>
                                        <small class="text-muted fw-bold text-uppercase ls-1">Tema Khutbah</small>
                                        <p class="text-dark mt-1 mb-0">"{{ $khotibJumatIni->tema_khutbah }}"</p>
                                    </div>

                                    <div class="mt-auto">
                                        <span class="tanggal-badge">
                                            <i class="bi bi-calendar-event me-2"></i>
                                            {{ $khotibJumatIni->tanggal->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                        <h5>Belum ada jadwal</h5>
                        <p>Jadwal khutbah Jumat untuk minggu ini belum tersedia.</p>
                    </div>
                @endif
            </div>

            {{-- KOLOM KANAN: List Khutbah Selanjutnya --}}
            <div class="col-lg-5">
                <h4 class="fw-bold mb-4 text-dark">Jadwal Selanjutnya</h4>

                @forelse($khotibSelanjutnya as $khotib)
                    <div class="card khotib-list-card">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ $khotib->foto_url }}" class="card-img" alt="Foto {{ $khotib->nama_khotib }}">
                            </div>
                            <div class="flex-grow-1 card-body">
                                <h6 class="fw-bold mb-1 text-dark">{{ $khotib->nama_khotib }}</h6>
                                <p class="small text-muted mb-1"><i class="bi bi-person me-1"></i> Imam:
                                    {{ $khotib->nama_imam }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $khotib->tanggal->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 border rounded bg-light">
                        <small>Tidak ada jadwal lanjutan.</small>
                    </div>
                @endforelse

                {{-- PAGINATION BUTTON (Style Bootstrap 5) --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $khotibSelanjutnya->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
