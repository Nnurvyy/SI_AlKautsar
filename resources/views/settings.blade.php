@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <h1>Pengaturan Masjid</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- PENTING: tambahkan enctype="multipart/form-data" untuk upload gambar --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
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
                
                <div class="mb-3">
                    <label for="foto_masjid" class="form-label">Foto Masjid (Landing Page)</label>
                    @if($settings->foto_masjid)
                        <img src="{{ Storage::url($settings->foto_masjid) }}" alt="Foto Masjid" class="img-thumbnail mb-2" width="300">
                    @endif
                    <input type="file" class="form-control" id="foto_masjid" name="foto_masjid">
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

{{-- 
PENTING! Jalankan perintah ini 1x di terminal Anda agar gambar bisa tampil:
php artisan storage:link 
--}}
@endsection