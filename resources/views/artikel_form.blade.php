@extends('layouts.app')

@section('title', 'Form Artikel')

@section('content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="container-fluid p-4" style="font-family: 'Poppins', sans-serif;">
    
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('pengurus.artikel.index') }}" class="btn btn-light rounded-circle shadow-sm me-3" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="fw-bold mb-0 text-dark">
            {{ isset($artikel) ? 'Ubah Artikel' : 'Tulis Artikel Baru' }}
        </h3>
    </div>
    
    <form id="artikelForm"
          action="{{ isset($artikel) 
                        ? route('pengurus.artikel.update', $artikel->id_artikel) 
                        : route('pengurus.artikel.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @if(isset($artikel))
            @method('PUT')
        @endif

        <div class="row g-4">

            {{-- KIRI: Editor --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">

                        {{-- Judul --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Judul Artikel</label>
                            <input type="text" class="form-control rounded-pill-input fs-5 fw-bold"
                                   name="judul_artikel"
                                   value="{{ old('judul_artikel', $artikel->judul_artikel ?? '') }}"
                                   placeholder="Masukkan Judul Artikel yang Menarik..."
                                   required>
                        </div>

                        {{-- Quill Editor --}}
                        <label class="form-label fw-bold text-success small text-uppercase ls-1">Isi Konten</label>
                        <div class="rounded-3 overflow-hidden border">
                            <div id="editor-container" style="height: 500px; background:#ffffff; font-family: 'Poppins', sans-serif;">
                                {!! old('isi_artikel', $artikel->isi_artikel ?? '') !!}
                            </div>
                        </div>

                        {{-- Hidden Field untuk Quill --}}
                        <input type="hidden" name="isi_artikel" id="isiArtikelInput">

                    </div>
                </div>
            </div>

            {{-- KANAN: Sidebar --}}
            <div class="col-lg-4">

                {{-- Opsi Penerbitan --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold text-dark">Opsi Penerbitan</h6>
                    </div>
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Status</label>
                            @php $currentStatus = old('status_artikel', $artikel->status_artikel ?? 'draft'); @endphp
                            <select class="form-select rounded-pill-input" name="status_artikel" required>
                                <option value="draft" {{ $currentStatus == 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                                <option value="published" {{ $currentStatus == 'published' ? 'selected' : '' }}>Published (Terbit)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Penulis</label>
                            <input type="text" class="form-control rounded-pill-input"
                                   name="penulis_artikel"
                                   value="{{ old('penulis_artikel', $artikel->penulis_artikel ?? (auth()->check() ? auth()->user()->name : 'Admin')) }}"
                                   required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold text-muted small">Tanggal Terbit</label>
                            @php
                                $tgl = old('tanggal_terbit_artikel', $artikel->tanggal_terbit_artikel ?? date('Y-m-d'));
                                if ($tgl instanceof \Carbon\Carbon) $tgl = $tgl->format('Y-m-d');
                            @endphp
                            <input type="date" class="form-control rounded-pill-input"
                                   name="tanggal_terbit_artikel"
                                   value="{{ $tgl }}"
                                   required>
                        </div>

                    </div>
                </div>

                {{-- Foto Artikel --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold text-dark">Foto Sampul</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <input type="file" class="d-none" id="foto_artikel" name="foto_artikel" accept="image/*">
                        <input type="hidden" name="hapus_foto" id="hapus_foto_input" value="0">
                        
                        {{-- Upload Placeholder --}}
                        <div class="mb-3">
                             <label for="foto_artikel" class="btn btn-outline-success w-100 rounded-pill border-dashed py-3" style="border-style: dashed; border-width: 2px;">
                                <i class="bi bi-camera-fill me-2"></i> Pilih Foto
                            </label>
                        </div>

                        {{-- Preview --}}
                        <div id="previewContainer" class="position-relative {{ isset($artikel) && $artikel->foto_artikel ? '' : 'd-none' }}">
    
                            {{-- PERUBAHAN ADA DI SINI (style) --}}
                            <img id="fotoPreview" 
                                    src="{{ isset($artikel) ? $artikel->foto_url : '' }}" 
                                    class="img-fluid rounded-3 shadow-sm w-100 border"
                                    style="width: 100%; aspect-ratio: 1/1; object-fit: cover;">
                                    {{-- aspect-ratio: 1/1 memaksa jadi kotak, object-fit: cover memotong gambar agar tidak gepeng --}}
                            
                            <button type="button" id="clearFile" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" style="width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2 text-center" style="font-size: 0.8rem;">Max 2MB (JPG/PNG)</small>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-gradient-green rounded-pill py-3 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-2"></i> 
                        {{ isset($artikel) ? 'Simpan Perubahan' : 'Terbitkan Artikel' }}
                    </button>
                    <a href="{{ route('pengurus.artikel.index') }}" class="btn btn-light rounded-pill py-2 text-muted fw-bold">
                        Batal
                    </a>
                </div>

            </div>
        </div>

    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    
    /* Global Styles for this page */
    .ls-1 { letter-spacing: 0.5px; }

    /* Button Gradient Green */
    .btn-gradient-green {
        background: linear-gradient(135deg, #198754, #20c997);
        border: none; color: white; transition: all 0.3s;
    }
    .btn-gradient-green:hover {
        background: linear-gradient(135deg, #157347, #198754);
        transform: translateY(-2px); color: white;
        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3) !important;
    }

    /* Rounded Inputs */
    .rounded-pill-input {
        border-radius: 50px !important;
        border: 1px solid #d1d5db;
        padding-left: 20px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }
</style>

{{-- Quill --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
{{-- JS Custom --}}
<script src="{{ asset('js/artikel_form.js') }}"></script>
@endsection