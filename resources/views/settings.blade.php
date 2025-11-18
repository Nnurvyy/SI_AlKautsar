@extends('layouts.app') 

@section('title', 'Pengaturan Masjid')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- ================================================= --}}
{{-- 1. TAMBAHKAN CSS TEMA BOOTSTRAP 5 UNTUK SELECT2 --}}
{{-- ================================================= --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

{{-- CSS untuk preview gambar --}}
<style>
    #clearFotoMasjid:hover { color: #212529; }
    #clearFotoMasjid:focus { box-shadow: none; }
    #foto_masjid_label { cursor: pointer; }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4"> 
    <h1>Pengaturan Masjid</h1>

    <form id="formSettings" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="nama_masjid" class="form-label">Nama Masjid</label>
                    <input type="text" class="form-control" id="nama_masjid" name="nama_masjid" value="{{ old('nama_masjid', $settings->nama_masjid) }}">
                </div>
                
                <div class="mb-3">
                    <label for="lokasi-select" class="form-label">Kabupaten/Kota</label>
                    
                    {{-- Ini adalah <select> yang akan dilihat user --}}
                    <select id="lokasi-select" 
                            name="lokasi_id_api" 
                            class="form-select">
                        {{-- Dibiarkan kosong, akan diisi oleh JS --}}
                    </select>
                    
                    {{-- Ini adalah input tersembunyi untuk menyimpan NAMA KOTA --}}
                    <input type="hidden" 
                        name="lokasi_nama_api" 
                        id="lokasi-nama-api" 
                        value="{{ $selectedLokasiText }}"> {{-- Ambil dari Controller --}}
                </div>


                <div class="mb-3">
                    <label for="lokasi_nama" class="form-label">Detail Alamat</label>
                    <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" value="{{ old('lokasi_nama', $settings->lokasi_nama) }}" placeholder="Contoh: Jl. K.H Abdul Halim No. 36, Majalengka Kulon, Majalengka, Jawa Barat">
                </div>

                
                @php
                    $foto_url = $settings->foto_masjid ? Storage::url($settings->foto_masjid) : '';
                    $foto_name = $settings->foto_masjid ? basename($settings->foto_masjid) : '';
                @endphp
                <div class="mb-3">
                    <label for="foto_masjid" class="form-label">Foto Masjid </label>
                    <input type="file" class="d-none" id="foto_masjid" name="foto_masjid" accept="image/*">
                    <small class="form-text">(jpg/jpeg/png/webp, max 2MB)</small>
                    
                    <label for="foto_masjid" id="foto_masjid_label" class="form-control d-block text-truncate position-relative">
                        <span class="{{ $foto_url ? '' : 'text-muted' }}">{{ $foto_name ?: 'Choose file...' }}</span>
                        
                        <button type="button" class="btn position-absolute {{ $foto_url ? '' : 'd-none' }}" id="clearFotoMasjid" title="Hapus foto" 
                                style="top: 50%; right: 0.3rem; transform: translateY(-50%); z-index: 5; padding: 0 0.5rem; font-size: 1.2rem; color: #6c757d; line-height: 1; background: transparent; border: 0;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </label>

                    <div id="previewFotoMasjidContainer" class="position-relative {{ $foto_url ? '' : 'd-none' }} mt-2">
                        <img id="previewFotoMasjid"
                             src="{{ $foto_url }}"
                             class="rounded mt-2 mx-auto d-block"
                             style="width: 200px; height: 200px; object-fit: cover;">
                    </div>
                    <small class="form-text">Kosongkan jika tidak ingin mengubah foto.</small>
                </div>

                <hr>
                <h5>Social Media</h5>
                
                <div class="mb-3">
                    <label for="social_instagram" class="form-label">Instagram URL</label>
                    <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings->social_instagram) }}" placeholder="https://instagram.com/namauser">
                </div>
                
                <div class="mb-3">
                    <label for="social_facebook" class="form-label">Facebook URL</label>
                    <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings->social_facebook) }}" placeholder="https://facebook.com/namauser">
                </div>

                <div class="mb-3">
                    <label for="social_youtube" class="form-label">Youtube URL</label>
                    <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $settings->social_youtube) }}" placeholder="https://youtube.com/channel">
                </div>

                <div class="mb-3">
                    <label for="social_whatsapp" class="form-label">Nomor WhatsApp</label>
                    <input type="text" class="form-control" id="social_whatsapp" name="social_whatsapp" value="{{ old('social_whatsapp', $settings->social_whatsapp) }}" placeholder="628123456789">
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Versi jQuery 3.6.0 atau 3.7.1 sama saja, keduanya berfungsi --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/settings.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lokasiSelect = $('#lokasi-select'); 
        const lokasiNamaApiInput = $('#lokasi-nama-api'); // Ambil hidden input
        
        // Ambil data yang DIKIRIM OLEH CONTROLLER
        const defaultLokasiId = @json($selectedLokasiId);
        const defaultLokasiText = @json($selectedLokasiText);

        // 1. Inisialisasi Select2
        lokasiSelect.select2({
            theme: "bootstrap-5",
            placeholder: 'Cari kota...',
            ajax: {
                url: function (params) {
                    var searchTerm = params.term || "";
                    return 'https://api.myquran.com/v2/sholat/kota/cari/' + searchTerm;
                },
                dataType: 'json',
                delay: 250, 
                processResults: function (data) {
                    if (data.status && data.data) {
                        return {
                            results: data.data.map(item => ({
                                id: item.id,
                                text: item.lokasi
                            }))
                        };
                    } else {
                        return { results: [] };
                    }
                },
                cache: true
            },
            minimumInputLength: 3 
        });

        // 2. Cek jika kita punya ID DAN Teks (Sama seperti jadwal-adzan)
        if (defaultLokasiId && defaultLokasiText) {
            
            // Buat <option> baru dari data yang DIKIRIM CONTROLLER
            var defaultOption = new Option(defaultLokasiText, defaultLokasiId, true, true);
            
            // Tambahkan option itu ke select
            lokasiSelect.append(defaultOption);
            
            // Beritahu Select2 untuk meng-update tampilannya
            lokasiSelect.trigger('change');
        }

        // 3. Listener PENTING saat memilih kota BARU
        lokasiSelect.on('select2:select', function (e) {
            // Ambil data (ID dan Teks) dari item yg dipilih
            var data = e.params.data;
            
            // Update hidden input dengan NAMA kota yang baru
            // Agar saat disubmit, datanya ikut terkirim ke controller
            lokasiNamaApiInput.val(data.text);
        });
    });
</script>
@endpush