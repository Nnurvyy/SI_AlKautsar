@extends('layouts.app') 

@section('title', 'Pengaturan Masjid')

@push('styles')
{{-- CSS Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

{{-- CSS Leaflet (Peta) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

    <form id="formSettings" enctype="multipart/form-data" action="{{ route('pengurus.settings.update') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                
                {{-- ================================================= --}}
                {{-- BAGIAN FOTO MASJID (BARU) --}}
                {{-- ================================================= --}}
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <label class="form-label fw-bold mb-3">Foto Masjid</label>
                        
                        {{-- Container Preview --}}
                        <div id="previewFotoMasjidContainer" class="mb-3 {{ $settings->foto_masjid ? '' : 'd-none' }}">
                            <img id="previewFotoMasjid" 
                                 src="{{ $settings->foto_masjid ? Storage::url($settings->foto_masjid) : '' }}" 
                                 alt="Preview Foto" 
                                 class="img-thumbnail shadow-sm" 
                                 style="max-width: 300px; max-height: 250px; object-fit: cover;">
                        </div>

                        {{-- Input & Tombol --}}
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <div class="d-inline-block">
                                <input class="form-control" type="file" id="foto_masjid" name="foto_masjid" accept="image/*">
                            </div>
                            
                            {{-- Tombol Hapus (Muncul jika ada foto) --}}
                            <button type="button" id="clearFotoMasjid" class="btn btn-outline-danger {{ $settings->foto_masjid ? '' : 'd-none' }}" title="Hapus Foto">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                        <div class="form-text">Format: JPG, PNG, WEBP. Maksimal 2MB.</div>
                        
                        {{-- Label dummy untuk logic JS Anda --}}
                        <div id="foto_masjid_label" class="d-none"><span></span></div>
                    </div>
                </div>

                <hr>

                {{-- Nama Masjid & Kota --}}
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
                {{-- BAGIAN PETA / LOKASI --}}
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

                {{-- Social Media --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp (No. HP)</label>
                        <input type="text" class="form-control" name="social_whatsapp" value="{{ $settings->social_whatsapp }}" placeholder="Contoh: 6281234567890">
                        <div class="form-text small">Gunakan format 62... tanpa spasi/strip.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-instagram text-danger me-1"></i> Instagram (Link Profil)</label>
                        <input type="text" class="form-control" name="social_instagram" value="{{ $settings->social_instagram }}" placeholder="https://instagram.com/...">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-facebook text-primary me-1"></i> Facebook (Link Halaman)</label>
                        <input type="text" class="form-control" name="social_facebook" value="{{ $settings->social_facebook }}" placeholder="https://facebook.com/...">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-youtube text-danger me-1"></i> YouTube (Link Channel)</label>
                        <input type="text" class="form-control" name="social_youtube" value="{{ $settings->social_youtube }}" placeholder="https://youtube.com/...">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-twitter text-info me-1"></i> Twitter / X (Link Profil)</label>
                        <input type="text" class="form-control" name="social_twitter" value="{{ $settings->social_twitter }}" placeholder="https://twitter.com/...">
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

{{-- JS LEAFLET --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // ==========================================================
        // 1. LOGIC PETA (LEAFLET)
        // ==========================================================
        let lat = "{{ $settings->latitude ?? -6.1753924 }}"; 
        let lng = "{{ $settings->longitude ?? 106.8271528 }}";
        lat = parseFloat(lat);
        lng = parseFloat(lng);

        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
            map.panTo(position);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            map.panTo(e.latlng);
        });

        L.Control.geocoder({ defaultMarkGeocode: false })
        .on('markgeocode', function(e) {
            const center = e.geocode.center;
            marker.setLatLng(center);
            updateInputs(center.lat, center.lng);
            map.setView(center, 16);
        })
        .addTo(map);

        document.getElementById('btnCurrentLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const newLatLng = new L.LatLng(userLat, userLng);
                    marker.setLatLng(newLatLng);
                    map.setView(newLatLng, 16);
                    updateInputs(userLat, userLng);
                    Swal.fire({ icon: 'success', title: 'Lokasi Ditemukan', timer: 1000, showConfirmButton: false });
                }, function() {
                    Swal.fire('Error', 'Gagal mengambil lokasi GPS.', 'error');
                });
            } else {
                Swal.fire('Error', 'Browser tidak mendukung Geolocation.', 'error');
            }
        });

        // ==========================================================
        // 2. LOGIC SELECT2 (KOTA) - JQUERY
        // ==========================================================
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

        // ==========================================================
        // 3. LOGIC SUBMIT FORM & UPLOAD GAMBAR (VANILLA JS)
        // ==========================================================
        const form = document.getElementById('formSettings');
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Elemen Gambar
        const fotoInput = document.getElementById('foto_masjid');
        const clearFileBtn = document.getElementById('clearFotoMasjid');
        const preview = document.getElementById('previewFotoMasjid');
        const previewContainer = document.getElementById('previewFotoMasjidContainer');
        const fotoLabelSpan = document.querySelector('#foto_masjid_label span'); // Dummy wrapper

        // --- SUBMIT FORM ---
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            const formData = new FormData(form);
            const url = form.getAttribute('action');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json' // Agar Laravel mengembalikan JSON jika validasi gagal
                    },
                    body: formData
                });

                const data = await res.json();
                
                if (res.ok) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    
                    if (data.foto_url) {
                         preview.src = data.foto_url;
                         previewContainer.classList.remove('d-none');
                         clearFileBtn.classList.remove('d-none');
                         fotoInput.value = ""; // Reset input file agar bisa pilih file yg sama lagi jika perlu
                    } else {
                        // Jika response tidak ada URL (berarti dihapus atau memang null)
                        if (!preview.src || preview.src.includes(window.location.origin)) {
                            // Cek apakah user meminta hapus
                            if(document.getElementById('hapus_foto_masjid')) {
                                clearFileVisuals();
                            }
                        }
                    }
                    
                    // Hapus hidden input 'hapus_foto_masjid' setelah sukses
                    const deleteInput = document.getElementById('hapus_foto_masjid');
                    if (deleteInput) deleteInput.remove();
                    
                } else {
                    if (res.status === 422 && data.errors) {
                        let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (err) {
                Swal.fire('Gagal', err.message, 'error');
            } finally {
                setLoading(false);
            }
        });

        function setLoading(isLoading) {
            if (isLoading) {
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        }

        // --- PREVIEW GAMBAR ---
        if (fotoInput) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Tampilkan tombol hapus
                    clearFileBtn.classList.remove('d-none');
                    
                    // Baca file untuk preview
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        previewContainer.classList.remove('d-none');
                    }
                    reader.readAsDataURL(file);
                    
                    // Jika user pilih file baru, batalkan status hapus (jika sebelumnya diklik hapus)
                    const deleteInput = document.getElementById('hapus_foto_masjid');
                    if (deleteInput) deleteInput.remove();
                }
            });
        }

        // --- HAPUS GAMBAR ---
        function clearFileVisuals() {
            clearFileBtn.classList.add('d-none');
            preview.src = "";
            previewContainer.classList.add('d-none');
        }

        if (clearFileBtn) {
            clearFileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                fotoInput.value = ""; // Kosongkan input file
                clearFileVisuals();   // Sembunyikan preview
                
                // Buat hidden input untuk memberitahu server agar menghapus file di DB
                let deleteInput = document.getElementById('hapus_foto_masjid');
                if (!deleteInput) {
                    deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'hapus_foto_masjid';
                    deleteInput.id = 'hapus_foto_masjid';
                    form.appendChild(deleteInput);
                }
                deleteInput.value = '1';
            });
        }
    });
</script>
@endpush