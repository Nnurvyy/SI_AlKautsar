@extends('layouts.app')

@section('title', 'Form Artikel')

@section('content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">
        {{ isset($artikel) ? 'Ubah Artikel: ' . ($artikel->judul_artikel ?? '') : 'Buat Artikel Baru' }}
    </h3>
    
    <form id="artikelForm"
          action="{{ isset($artikel) 
                        ? route('admin.artikel.update', $artikel->id_artikel) 
                        : route('admin.artikel.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @if(isset($artikel))
            @method('PUT')
        @endif

        <div class="row">

            {{-- KIRI --}}
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

                        {{-- Hidden Field --}}
                        <input type="hidden" name="isi_artikel" id="isiArtikelInput">

                    </div>
                </div>
            </div>

            {{-- KANAN --}}
            <div class="col-md-4">

                {{-- Status --}}
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

                        {{-- Penulis --}}
                        <div class="mb-3">
                            <label class="form-label">Penulis</label>
                            <input type="text" class="form-control"
                                   name="penulis_artikel"
                                   value="{{ old('penulis_artikel', $artikel->penulis_artikel ?? (auth()->check() ? auth()->user()->name : 'Admin')) }}"
                                   required>
                        </div>

                        {{-- Tanggal --}}
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

                {{-- Foto --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Foto Artikel (Featured Image)</div>
                    <div class="card-body">

                        <div class="text-center mb-3">
                            <img id="fotoPreview"
                                 src="{{ $artikel->foto_url ?? asset('images/default_artikel.png') }}"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height:150px;object-fit:cover;">
                        </div>

                        <input type="file" class="form-control" id="foto_artikel" name="foto_artikel" accept="image/*">
                        <small class="text-muted mt-2 d-block">Max 2MB (opsional jika tidak ingin ganti foto).</small>

                    </div>
                </div>

            </div>
        </div>

        {{-- Footer Button --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary me-2">Batal</a>
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
