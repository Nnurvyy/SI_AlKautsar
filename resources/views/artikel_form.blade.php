@extends('layouts.app')

@section('title', 'Form Artikel')

@section('content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">
        {{ isset($artikel) ? 'Ubah Artikel' : 'Buat Artikel Baru' }}
    </h3>
    
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

        <div class="row">

            {{-- KIRI: Editor --}}
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">

                        {{-- Judul --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Artikel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg"
                                   name="judul_artikel"
                                   value="{{ old('judul_artikel', $artikel->judul_artikel ?? '') }}"
                                   placeholder="Masukkan Judul Artikel"
                                   required>
                        </div>

                        {{-- Quill Editor --}}
                        <label class="form-label fw-bold">Isi Konten Artikel <span class="text-danger">*</span></label>
                        <div id="editor-container" style="height: 400px; background:#ffffff;">
                            {!! old('isi_artikel', $artikel->isi_artikel ?? '') !!}
                        </div>

                        {{-- Hidden Field untuk Quill --}}
                        <input type="hidden" name="isi_artikel" id="isiArtikelInput">

                    </div>
                </div>
            </div>

            {{-- KANAN: Sidebar --}}
            <div class="col-md-4">

                {{-- Status & Meta --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Opsi Penerbitan</div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            @php $currentStatus = old('status_artikel', $artikel->status_artikel ?? 'draft'); @endphp
                            <select class="form-select" name="status_artikel" required>
                                <option value="draft" {{ $currentStatus == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ $currentStatus == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penulis</label>
                            <input type="text" class="form-control"
                                   name="penulis_artikel"
                                   value="{{ old('penulis_artikel', $artikel->penulis_artikel ?? (auth()->check() ? auth()->user()->name : 'Admin')) }}"
                                   required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Tanggal Terbit</label>
                            @php
                                $tgl = old('tanggal_terbit_artikel', $artikel->tanggal_terbit_artikel ?? date('Y-m-d'));
                                if ($tgl instanceof \Carbon\Carbon) $tgl = $tgl->format('Y-m-d');
                            @endphp
                            <input type="date" class="form-control"
                                   name="tanggal_terbit_artikel"
                                   value="{{ $tgl }}"
                                   required>
                        </div>

                    </div>
                </div>

                {{-- FOTO ARTIKEL (Updated UI) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Foto Artikel (Featured Image)</div>
                    <div class="card-body">
                        
                        <input type="file" class="d-none" id="foto_artikel" name="foto_artikel" accept="image/*">
                        
                        <input type="hidden" name="hapus_foto" id="hapus_foto_input" value="0">
                        
                        <div class="position-relative mb-3">
                            <label for="foto_artikel" id="foto_artikel_label" class="form-control d-block text-truncate" style="cursor: pointer; padding-right: 40px;">
                                <span class="text-muted">Pilih foto...</span>
                            </label>
                            
                            <button type="button" class="btn position-absolute d-none" id="clearFile" title="Hapus foto" 
                                    style="top: 50%; right: 5px; transform: translateY(-50%); z-index: 5; padding: 4px 8px; color: #dc3545; border: none; background: transparent; font-size: 1.2rem;">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>

                        <div id="previewContainer" class="text-center {{ isset($artikel) && $artikel->foto_artikel ? '' : 'd-none' }}"
                            data-has-image="{{ isset($artikel) && $artikel->foto_artikel ? 'true' : 'false' }}"
                            data-original-src="{{ isset($artikel) ? $artikel->foto_url : '' }}">
                            
                            <img id="fotoPreview" 
                                src="{{ isset($artikel) ? $artikel->foto_url : '' }}" 
                                class="img-fluid rounded shadow-sm border"
                                style="max-height: 200px; object-fit: cover; width: 100%;">
                        </div>

                        <small class="text-muted mt-2 d-block">Max 2MB. Format: JPG, PNG.</small>

                    </div>
                </div>

            </div>
        </div>

        {{-- Footer Button --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('pengurus.artikel.index') }}" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save me-1"></i>
                {{ isset($artikel) ? 'Update Artikel' : 'Simpan Artikel' }}
            </button>
        </div>

    </form>
</div>

{{-- Quill --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
{{-- JS Custom --}}
<script src="{{ asset('js/artikel_form.js') }}"></script>
@endsection