@extends('layouts.public')

@section('title', 'Tentang Kami - Masjid ' . $masjidSettings->nama_masjid)

@push('styles')
    <style>
        .about-info-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: relative;
            z-index: 2;
            margin-bottom: 2rem;
            padding: 0;
        }

        .about-header-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .about-content-body {
            padding: 2rem;
        }

        .map-container iframe {
            width: 100%;
            height: 300px;
            border-radius: 15px;
            border: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .contact-btn {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            margin-bottom: 0.8rem;
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .contact-btn:hover {
            transform: translateY(-3px);
            color: white;
        }

        .contact-btn i {
            font-size: 1.4rem;
            margin-right: 0.8rem;
        }

        .btn-wa {
            background: linear-gradient(45deg, #25D366, #128C7E);
        }

        .btn-ig {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }

        .btn-fb {
            background: linear-gradient(45deg, #3b5998, #192f5d);
        }

        .btn-yt {
            background: linear-gradient(45deg, #FF0000, #cc0000);
        }

        .btn-tw {
            background: linear-gradient(45deg, #1DA1F2, #0d8bd9);
        }

        @media (min-width: 992px) {
            .about-content-body {
                padding: 2.5rem;
            }
        }
    </style>
@endpush

@section('content')

    {{-- PERUBAHAN: Max-width diperkecil jadi 700px agar ramping --}}
    <div class="container mt-4" style="max-width: 700px;">

        {{-- 1. INFO MASJID & DESKRIPSI (Card Utama) --}}
        <div class="about-info-card">

            {{-- A. Gambar Masuk ke Sini (Paling Atas) --}}
            {{-- height: auto memastikan gambar tampil utuh sesuai aslinya --}}
            <img src="{{ $masjidSettings->foto_masjid ? Storage::url($masjidSettings->foto_masjid) : asset('images/masjid.jpeg') }}"
                class="about-header-img" alt="Foto Masjid">

            {{-- B. Konten Teks --}}
            <div class="about-content-body text-center">
                <h2 class="fw-bold text-dark mb-1">{{ $masjidSettings->nama_masjid }}</h2>
                <p class="text-muted mb-4"><i class="bi bi-geo-alt-fill text-danger"></i> {{ $masjidSettings->lokasi_nama }}
                </p>

                <hr class="w-25 mx-auto mb-4">

                <div class="text-start text-secondary" style="line-height: 1.8; white-space: pre-line;">
                    @if ($masjidSettings->deskripsi_masjid)
                        {!! nl2br(e($masjidSettings->deskripsi_masjid)) !!}
                    @else
                        <p class="text-center text-muted fst-italic">Belum ada deskripsi profil masjid.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. LOKASI (LEAFLET MAP READ-ONLY) --}}
        @if ($masjidSettings->latitude && $masjidSettings->longitude)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-map-fill me-2 text-primary"></i>Lokasi Kami</h5>
                        {{-- Tombol Buka di Google Maps Asli --}}
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $masjidSettings->latitude }},{{ $masjidSettings->longitude }}"
                            target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-box-arrow-up-right"></i> Buka Google Maps
                        </a>
                    </div>
                    {{-- Container Map --}}
                    <div id="publicMap" style="height: 300px; width: 100%;"></div>
                </div>
            </div>

            {{-- Load Leaflet CSS & JS khusus halaman ini --}}
            @push('styles')
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            @endpush

            @push('scripts')
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const lat = {{ $masjidSettings->latitude }};
                        const lng = {{ $masjidSettings->longitude }};

                        const map = L.map('publicMap').setView([lat, lng], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);

                        L.marker([lat, lng]).addTo(map)
                            .bindPopup("<b>{{ $masjidSettings->nama_masjid }}</b><br>{{ $masjidSettings->lokasi_nama }}")
                            .openPopup();
                    });
                </script>
            @endpush
        @endif

        {{-- 3. KONTAK KAMI (SOCIAL MEDIA) --}}
        <div class="mb-5">
            <h5 class="fw-bold mb-3 ps-2 border-start border-4 border-primary">Hubungi Kami</h5>
            <div class="row">
                @if ($masjidSettings->social_whatsapp)
                    <div class="col-md-6">
                        <a href="https://wa.me/{{ $masjidSettings->social_whatsapp }}" target="_blank"
                            class="contact-btn btn-wa">
                            <i class="bi bi-whatsapp"></i>
                            <div>
                                <div class="small opacity-75">WhatsApp</div>
                                <div>Chat Pengurus</div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($masjidSettings->social_instagram)
                    <div class="col-md-6">
                        <a href="{{ $masjidSettings->social_instagram }}" target="_blank" class="contact-btn btn-ig">
                            <i class="bi bi-instagram"></i>
                            <div>
                                <div class="small opacity-75">Instagram</div>
                                <div>Follow Kami</div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($masjidSettings->social_facebook)
                    <div class="col-md-6">
                        <a href="{{ $masjidSettings->social_facebook }}" target="_blank" class="contact-btn btn-fb">
                            <i class="bi bi-facebook"></i>
                            <div>
                                <div class="small opacity-75">Facebook</div>
                                <div>Halaman Resmi</div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($masjidSettings->social_youtube)
                    <div class="col-md-6">
                        <a href="{{ $masjidSettings->social_youtube }}" target="_blank" class="contact-btn btn-yt">
                            <i class="bi bi-youtube"></i>
                            <div>
                                <div class="small opacity-75">YouTube</div>
                                <div>Tonton Kajian</div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
