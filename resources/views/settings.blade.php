@extends('layouts.app') 

@section('title', 'Pengaturan Masjid')

@push('styles')
{{-- CSS untuk preview gambar --}}
<style>
    #clearFotoMasjid:hover { color: #212529; }
    #clearFotoMasjid:focus { box-shadow: none; }
    #foto_masjid_label { cursor: pointer; }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4"> 
    <h1>Pengaturan Masjid</h1>

    {{-- Container untuk notifikasi AJAX (dihapus karena pakai SweetAlert) --}}
    {{-- <div id="alertContainer"></div> --}}

    <form id="formSettings" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="nama_masjid" class="form-label">Nama Masjid</label>
                    <input type="text" class="form-control" id="nama_masjid" name="nama_masjid" value="{{ old('nama_masjid', $settings->nama_masjid) }}">
                </div>

                <div class="mb-3">
                    <label for="lokasi_nama" class="form-label">Nama Lokasi (Tampilan)</label>
                    <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" value="{{ old('lokasi_nama', $settings->lokasi_nama) }}" placeholder="Contoh: Bandung, Jawa Barat">
                </div>

                <div class="mb-3">
                    <label for="lokasi_id_api" class="form-label">ID Lokasi (Untuk Jadwal Adzan)</label>
                    <input type="text" class="form-control" id="lokasi_id_api" name="lokasi_id_api" value="{{ old('lokasi_id_api', $settings->lokasi_id_api) }}" placeholder="Contoh: 1204 (Untuk Bandung)">
                    <small class="form-text">Cari ID Kota di API MyQuran (misal: 1204 untuk Bandung, 1218 untuk Tasikmalaya).</small>
                </div>
                
                @php
                    $foto_url = $settings->foto_masjid ? Storage::url($settings->foto_masjid) : '';
                    $foto_name = $settings->foto_masjid ? basename($settings->foto_masjid) : '';
                @endphp
                <div class="mb-3">
                    <label for="foto_masjid" class="form-label">Foto Masjid (jpg/png/webp, max 2MB)</label>
                    <input type="file" class="d-none" id="foto_masjid" name="foto_masjid" accept="image/*">
                    
                    {{-- ================================================= --}}
                    {{-- PERBAIKAN: Memindahkan Tombol X ke dalam Label --}}
                    {{-- ================================================= --}}
                    
                    {{-- Hapus <div> wrapper, tambahkan 'position-relative' ke label --}}
                    <label for="foto_masjid" id="foto_masjid_label" class="form-control d-block text-truncate position-relative">
                        <span class="{{ $foto_url ? '' : 'text-muted' }}">{{ $foto_name ?: 'Choose file...' }}</span>
                        
                        {{-- Tombol X sekarang ada di DALAM label --}}
                        <button type="button" class="btn position-absolute {{ $foto_url ? '' : 'd-none' }}" id="clearFotoMasjid" title="Hapus foto" 
                                style="top: 50%; right: 0.3rem; transform: translateY(-50%); z-index: 5; padding: 0 0.5rem; font-size: 1.2rem; color: #6c757d; line-height: 1; background: transparent; border: 0;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </label>

                    {{-- Preview Container (Tidak Berubah) --}}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/settings.js') }}"></script>
@endpush