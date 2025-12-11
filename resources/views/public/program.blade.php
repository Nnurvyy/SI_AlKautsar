@extends('layouts.public')

@section('title', 'Program Masjid')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
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

        .swiper-button-prev {
            left: 20px;
        }

        .swiper-button-next {
            right: 20px;
        }

        .program-slide {
            position: relative;
            width: 100%;
            height: auto;
            aspect-ratio: 1/1 !important;
            border-radius: 16px;
            overflow: hidden;
            color: white;
        }

        .program-slide-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .program-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.2));
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
            margin-bottom: 1rem;
            line-height: 1.5;
            font-weight: 500;
            color: #f8f9fa;
        }

        .btn-slide-detail {
            background-color: white;
            color: #333;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-slide-detail:hover {
            background-color: #f0f0f0;
            color: #000;
            transform: scale(1.05);
        }

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
            width: 100%;
            height: 100%;
            aspect-ratio: 1/1 !important;
            border-radius: 12px 0 0 12px !important;
        }

        .program-list-card .col-img {
            display: flex;
            align-items: stretch;
            padding: 0;
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

        .program-list-card .card-date,
        .program-list-card .card-location {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
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

        .text-success-custom {
            color: #27ae60;
        }

        .text-warning-custom {
            color: #f39c12;
        }

        .text-secondary-custom {
            color: #7f8c8d;
        }

        @media (min-width: 768px) {
            .program-list-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }

        @media (min-width: 992px) {
            .program-list-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        #modalProgramImg {
            width: 100%;
            aspect-ratio: 1/1 !important;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            background-color: #f8f9fa;
        }

        .modal-program-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.2rem;
        }

        .modal-program-value {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .modal-program-desc {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #444;
        }
    </style>
@endpush

@section('content')

    {{-- 1. JUDUL HALAMAN --}}
    <div class="container pt-4 pb-3">
        <h2 class="program-title-heading">Program & Workshop</h2>
        <p class="program-title-sub">Ikuti kegiatan dan workshop terbaru dari kami untuk meningkatkan keimanan dan wawasan.
        </p>
    </div>

    {{-- 2. SLIDER PROGRAM --}}
    @if ($sliderPrograms->count() > 0)
        <div class="container mb-5">
            <div class="swiper-container-wrapper">
                <div class="swiper program-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($sliderPrograms as $item)
                            <div class="swiper-slide">
                                <div class="program-slide">
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama_program }}"
                                        class="program-slide-img">
                                    <div class="program-slide-overlay"></div>
                                    <div class="program-slide-content">
                                        <span class="program-slide-badge">Akan Datang</span>
                                        <h5>{{ $item->nama_program }}</h5>
                                        <p>
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $item->tanggal_program->translatedFormat('d F Y, H:i') }} WIB
                                        </p>
                                        {{-- Tombol Detail di Slider --}}
                                        <button type="button" class="btn-slide-detail"
                                            onclick="showDetailProgram('{{ $item->id_program }}')">
                                            Lihat Detail
                                        </button>
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
        @if ($semuaProgram->isEmpty())
            <div class="alert alert-info text-center py-5 rounded-3 shadow-sm">
                <i class="bi bi-info-circle fs-1 mb-3 d-block text-info"></i>
                <h5 class="fw-bold">Belum ada Program</h5>
                <p class="mb-0">Saat ini belum ada data program atau kegiatan yang tersedia.</p>
            </div>
        @else
            <div class="program-list-grid">
                @foreach ($semuaProgram as $program)
                    @php
                        $badgeColor = match ($program->status_program) {
                            'belum dilaksanakan' => 'text-success-custom',
                            'sedang berjalan' => 'text-warning-custom',
                            'sudah dijalankan' => 'text-secondary-custom',
                            default => 'text-primary',
                        };
                        $statusLabel = ucwords($program->status_program ?? 'Program');
                    @endphp

                    <div class="card program-list-card">
                        <div class="row g-0 h-100">

                            {{-- UPDATE: Ganti col-4 jadi col-5 agar gambar kotak proporsional --}}
                            <div class="col-5 col-img">
                                <img src="{{ $program->foto_url }}" class="card-img" alt="{{ $program->nama_program }}">
                            </div>

                            {{-- UPDATE: Ganti col-8 jadi col-7 --}}
                            <div class="col-7">
                                <div class="card-body">
                                    <div>
                                        <div class="card-badge {{ $badgeColor }}">{{ $statusLabel }}</div>
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
                                    <div class="text-end">
                                        <button type="button" class="btn-detail-small"
                                            onclick="showDetailProgram('{{ $program->id_program }}')">
                                            Lihat Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PAGINATION BUTTON (Style Bootstrap 5) --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $semuaProgram->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL PROGRAM --}}
    <div class="modal fade" id="modalProgramPublic" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalProgramTitle">Loading...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Loading Spinner --}}
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"><span
                                class="visually-hidden">Loading...</span></div>
                    </div>

                    {{-- Konten Detail --}}
                    <div id="modalContent" class="d-none">
                        {{-- Gambar di Modal juga 1:1 --}}
                        <img id="modalProgramImg" src="" alt="Detail Foto">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="modal-program-label">Waktu Pelaksanaan</div>
                                <div class="modal-program-value">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i> <span
                                        id="modalProgramDate"></span><br>
                                    <i class="bi bi-clock me-2 text-primary"></i> <span id="modalProgramTime"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modal-program-label">Lokasi</div>
                                <div class="modal-program-value">
                                    <i class="bi bi-geo-alt-fill me-2 text-danger"></i> <span id="modalProgramLoc"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modal-program-label">Penyelenggara</div>
                                <div class="modal-program-value">
                                    <i class="bi bi-building me-2 text-info"></i> <span id="modalProgramOrganizer"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modal-program-label">Status</div>
                                <div class="modal-program-value">
                                    <span class="badge bg-light text-dark border" id="modalProgramStatus"></span>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-2">Deskripsi Program</h6>
                        <div id="modalProgramDesc" class="modal-program-desc"></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.program-swiper')) {
                new Swiper('.program-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 15,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    navigation: {
                        nextEl: '.program-button-next',
                        prevEl: '.program-button-prev'
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


        function showDetailProgram(id) {
            const modal = new bootstrap.Modal(document.getElementById('modalProgramPublic'));
            modal.show();


            document.getElementById('modalLoading').classList.remove('d-none');
            document.getElementById('modalContent').classList.add('d-none');
            document.getElementById('modalProgramTitle').innerText = 'Memuat...';

            fetch(`/program/detail/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Gagal');
                    return res.json();
                })
                .then(data => {
                    document.getElementById('modalProgramTitle').innerText = data.nama_program;
                    document.getElementById('modalProgramImg').src = data.foto_url_lengkap;
                    document.getElementById('modalProgramDate').innerText = data.tanggal_formatted;
                    document.getElementById('modalProgramTime').innerText = data.waktu_formatted;
                    document.getElementById('modalProgramLoc').innerText = data.lokasi_program;
                    document.getElementById('modalProgramOrganizer').innerText = data.penyelenggara_program;
                    document.getElementById('modalProgramStatus').innerText = data.status_label;

                    document.getElementById('modalProgramDesc').innerHTML = data.deskripsi_program.replace(/\n/g,
                        '<br>');

                    document.getElementById('modalLoading').classList.add('d-none');
                    document.getElementById('modalContent').classList.remove('d-none');
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('modalProgramTitle').innerText = 'Terjadi Kesalahan';
                    document.getElementById('modalLoading').classList.add('d-none');
                });
        }
    </script>
@endpush
