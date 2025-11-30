@extends('layouts.app') 

@section('title', 'Pengaturan Masjid')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

{{-- 1. CSS LEAFLET (PETA) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
{{-- 2. CSS LEAFLET GEOCODER (PENCARIAN) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
    /* Container Peta */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 10px;
        z-index: 1;
    }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4"> 
    <h1>Pengaturan Masjid</h1>

    <form id="formSettings" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                
                {{-- Nama Masjid & Kota (Kode Lama) --}}
                <div class="mb-3">
                    <label class="form-label">Nama Masjid</label>
                    <input type="text" class="form-control" name="nama_masjid" value="{{ $settings->nama_masjid }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kabupaten/Kota (Jadwal Sholat)</label>
                    <select id="lokasi-select" name="lokasi_id_api" class="form-select"></select>
                    <input type="hidden" name="lokasi_nama_api" id="lokasi-nama-api" value="{{ $selectedLokasiText }}">
                </div>

                {{-- ================================================= --}}
                {{-- BAGIAN PETA / LOKASI (SHOPEE STYLE) --}}
                {{-- ================================================= --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Lokasi Masjid (Peta)</label>
                    <p class="text-muted small mb-2">Geser pin biru ke lokasi masjid yang tepat</p>
                    
                    {{-- Wrapper Peta --}}
                    <div class="position-relative mb-3">
                        <div id="map"></div>
                        
                        {{-- Tombol Ambil Lokasi Saat Ini --}}
                        <button type="button" id="btnCurrentLocation" class="btn btn-light btn-sm position-absolute shadow-sm" style="top: 10px; right: 10px; z-index: 400;">
                            <i class="bi bi-geo-alt-fill text-primary"></i> Lokasi Saya
                        </button>
                    </div>

                    {{-- Input Koordinat (Otomatis Terisi) --}}
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="small text-muted">Latitude</label>
                            <input type="text" class="form-control bg-light" id="latitude" name="latitude" value="{{ $settings->latitude }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Longitude</label>
                            <input type="text" class="form-control bg-light" id="longitude" name="longitude" value="{{ $settings->longitude }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Detail Alamat (Teks)</label>
                    <input type="text" class="form-control" name="lokasi_nama" value="{{ $settings->lokasi_nama }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi / Tentang Kami</label>
                    <textarea class="form-control" name="deskripsi_masjid" rows="3">{{ $settings->deskripsi_masjid }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp (No. HP)</label>
                        <input type="text" class="form-control" name="social_whatsapp" value="{{ $settings->social_whatsapp }}" placeholder="Contoh: 6281234567890">
                        <div class="form-text small">Gunakan format 62... tanpa spasi atau tanda strip.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-instagram text-danger me-1"></i> Instagram (Link Profil)</label>
                        <input type="text" class="form-control" name="social_instagram" value="{{ $settings->social_instagram }}" placeholder="Contoh: https://instagram.com/masjid_alkautsar">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-facebook text-primary me-1"></i> Facebook (Link Halaman)</label>
                        <input type="text" class="form-control" name="social_facebook" value="{{ $settings->social_facebook }}" placeholder="Contoh: https://facebook.com/masjidalkautsar">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-youtube text-danger me-1"></i> YouTube (Link Channel)</label>
                        <input type="text" class="form-control" name="social_youtube" value="{{ $settings->social_youtube }}" placeholder="Contoh: https://youtube.com/@masjidalkautsar">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-twitter text-info me-1"></i> Twitter / X (Link Profil)</label>
                        <input type="text" class="form-control" name="social_twitter" value="{{ $settings->social_twitter }}" placeholder="Contoh: https://twitter.com/masjidalkautsar">
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- 3. JS LEAFLET --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    // --- SETUP MAPS ---
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Ambil Koordinat Awal (Dari DB atau Default ke Monas Jakarta)
        let lat = "{{ $settings->latitude ?? -6.1753924 }}"; 
        let lng = "{{ $settings->longitude ?? 106.8271528 }}";
        
        // Konversi ke float
        lat = parseFloat(lat);
        lng = parseFloat(lng);

        // 2. Inisialisasi Map
        const map = L.map('map').setView([lat, lng], 15); // Zoom level 15

        // 3. Pasang Tile Layer (OpenStreetMap - Gratis)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // 4. Buat Marker (Pin Merah) yang bisa digeser (draggable)
        const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        // Fungsi update input saat pin digeser
        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        // Event: Saat marker selesai digeser
        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
            map.panTo(position); // Geser map ke tengah pin
        });

        // Event: Saat peta diklik (Pindah pin ke lokasi klik)
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            map.panTo(e.latlng);
        });

        // 5. Tambahkan Fitur Pencarian (Geocoder)
        L.Control.geocoder({
            defaultMarkGeocode: false
        })
        .on('markgeocode', function(e) {
            const bbox = e.geocode.bbox;
            const poly = L.polygon([
                bbox.getSouthEast(),
                bbox.getNorthEast(),
                bbox.getNorthWest(),
                bbox.getSouthWest()
            ]);
            map.fitBounds(poly.getBounds());
            
            // Pindahkan marker ke hasil pencarian
            const center = e.geocode.center;
            marker.setLatLng(center);
            updateInputs(center.lat, center.lng);
        })
        .addTo(map);

        // 6. Fitur "Ambil Lokasi Saya"
        document.getElementById('btnCurrentLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    
                    const newLatLng = new L.LatLng(userLat, userLng);
                    marker.setLatLng(newLatLng);
                    map.setView(newLatLng, 16);
                    updateInputs(userLat, userLng);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Lokasi Ditemukan',
                        text: 'Pin dipindahkan ke lokasi Anda saat ini.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }, function() {
                    Swal.fire('Error', 'Gagal mengambil lokasi GPS. Pastikan izin lokasi aktif.', 'error');
                });
            } else {
                Swal.fire('Error', 'Browser Anda tidak mendukung Geolocation.', 'error');
            }
        });

        // --- SUBMIT FORM (AJAX) ---
        $('#formSettings').on('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('pengurus.settings.update') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire('Berhasil', res.message, 'success');
                },
                error: function(err) {
                    Swal.fire('Gagal', 'Terjadi kesalahan validasi.', 'error');
                }
            });
        });

        // --- Logic Select2 Kota (Kode Lama Anda) ---
        // (Paste kode JS Select2 yang lama di sini agar fitur kota tetap jalan)
        const lokasiSelect = $('#lokasi-select');
        const lokasiNamaApiInput = $('#lokasi-nama-api');
        const defaultLokasiId = @json($selectedLokasiId);
        const defaultLokasiText = @json($selectedLokasiText);

        lokasiSelect.select2({
            theme: "bootstrap-5",
            placeholder: 'Cari kota...',
            ajax: {
                url: function (params) { return 'https://api.myquran.com/v2/sholat/kota/cari/' + params.term; },
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return { results: data.data ? data.data.map(item => ({ id: item.id, text: item.lokasi })) : [] };
                }
            },
            minimumInputLength: 3
        });

        if (defaultLokasiId && defaultLokasiText) {
            lokasiSelect.append(new Option(defaultLokasiText, defaultLokasiId, true, true)).trigger('change');
        }

        lokasiSelect.on('select2:select', function (e) {
            lokasiNamaApiInput.val(e.params.data.text);
        });
    });
</script>
@endpush