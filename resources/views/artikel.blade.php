@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- HEADER & SEARCH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            
            {{-- Filter Status --}}
            <select class="form-select rounded-pill ps-3" id="statusFilter" style="width: 160px; border-color: #e5e7eb;">
                <option value="all" selected>Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>

            {{-- Search Bar --}}
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari judul artikel...">
            </div>
        </div>

        {{-- Tombol Tambah --}}
        <a href="{{ route('pengurus.artikel.create') }}" class="btn btn-gradient-green rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Tulis Artikel
        </a>
    </div>

    {{-- CARD TOTAL ARTIKEL (Div sebelum Table) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Total Artikel</h5>
                <small class="text-muted">Jumlah artikel yang ditulis</small>
            </div>
            <h3 class="fw-bold text-success mb-0">
                {{ number_format($totalArtikel, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelartikel">
                    <thead class="bg-light">
                        <tr style="height: 50px;">
                            <th class="text-center ps-4 rounded-top-left" style="width: 5%;">No</th>
                            <th class="text-center" style="width: 10%;">Foto</th>
                            <th style="width: 30%;">Judul Artikel</th>
                            <th style="width: 15%;">Penulis</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-center" style="width: 15%;">Terbit</th>
                            <th class="text-center pe-4 rounded-top-right" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        {{-- Data dimuat lewat JS --}}
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        <div class="card-footer bg-white border-0 py-3">
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center">
                <span id="paginationInfo" class="text-muted small ms-2"></span>
                <nav id="paginationLinks" class="me-2"></nav>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL ARTIKEL (Clean Style) --}}
<div class="modal fade" id="modalDetailArtikel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg modal-rounded">

            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark">Preview Artikel</h5>
                    <p class="text-muted small mb-0">Detail konten dan informasi publikasi</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Foto --}}
                    <div class="col-md-4">
                        <img id="detailFotoArtikel" src="" class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover" style="max-height: 300px;">
                        
                        <div class="mt-3 p-3 bg-light rounded-3 border">
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr>
                                    <td class="text-muted fw-bold">Penulis</td>
                                    <td class="text-end fw-bold text-dark" id="d_penulis"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Status</td>
                                    <td class="text-end" id="d_status_artikel"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Tanggal</td>
                                    <td class="text-end text-dark" id="d_tanggal_terbit"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Konten --}}
                    <div class="col-md-8">
                        <h3 id="detailJudulArtikel" class="fw-bold text-dark mb-3" style="font-family: 'Poppins', sans-serif;"></h3>
                        <hr class="text-muted opacity-25">
                        <div id="d_isi" class="article-content text-dark" style="line-height: 1.8;"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- CSS KHUSUS (SAMA DENGAN DONASI & PROGRAM) --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

    /* Scoping Font Poppins */
    #tabelartikel, .card, .modal-content { 
        font-family: 'Poppins', sans-serif; 
    }

    /* --- GLOBAL STYLES --- */
    .modal-rounded { border-radius: 20px !important; overflow: hidden; }

    /* Button Gradient Green */
    .btn-gradient-green {
        background: linear-gradient(135deg, #198754, #20c997);
        border: none; color: white; transition: all 0.3s;
    }
    .btn-gradient-green:hover {
        background: linear-gradient(135deg, #157347, #198754);
        transform: translateY(-1px); color: white;
    }

    /* --- INPUT STYLES --- */
    .form-control:focus, .form-select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }

    /* --- TABLE --- */
    .rounded-top-left { border-top-left-radius: 10px; }
    .rounded-top-right { border-top-right-radius: 10px; }

    /* Pagination */
    #paginationLinks .pagination { margin-bottom: 0; }
    #paginationLinks .page-link { cursor: pointer; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/artikel.js') }}"></script>
@endsection