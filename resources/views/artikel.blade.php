@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Header: Filter, Search, Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center">
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="all" selected>Semua</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="input-group search-bar me-2 mb-2 mb-md-0" style="width: 350px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari Judul...">
            </div>
        </div>
        
        {{-- Tombol Tambah Artikel --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <a href="{{ route('pengurus.artikel.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Artikel
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Artikel</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelartikel">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 10%;" class="text-center">Foto</th>
                            <th scope="col" style="width: 25%;" class="text-center">Judul Artikel</th>
                            <th scope="col" style="width: 15%;" class="text-center">Penulis Artikel</th>
                            <th scope="col" style="width: 10%;" class="text-center">Status</th>
                            <th scope="col" style="width: 15%;" class="text-center">Tanggal Terbit</th>
                            <th scope="col" style="width: 20%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data dimuat lewat JS --}}
                    </tbody>
                </table>
            </div>
            
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo">Menampilkan 0 dari 0 data</span>
                <nav id="paginationLinks"></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailArtikel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header py-2">
        <h5 class="modal-title fw-bold">Detail Artikel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row">

          <div class="col-md-4 text-center mb-3">
              <img id="detailFotoArtikel" src="#" class="img-fluid rounded shadow-sm"
                   style="max-height: 250px; object-fit: cover;">
          </div>

          <div class="col-md-8">
              <h4 id="detailJudulArtikel" class="fw-bold mb-3"></h4>
              <table class="table table-sm table-borderless">
                  <tbody>
                      <tr><td class="fw-bold" width="120">Penulis</td><td>: <span id="d_penulis"></span></td></tr>
                      <tr><td class="fw-bold">Status</td><td>: <span id="d_status_artikel"></span></td></tr>
                      <tr><td class="fw-bold">Tanggal Terbit</td><td>: <span id="d_tanggal_terbit"></span></td></tr>
                  </tbody>
              </table>
          </div>

          <div class="col-12 mt-3">
              <hr>
              <h6 class="fw-bold">Isi Artikel:</h6>
              <div id="d_isi" class="p-3 border rounded bg-light"></div>
          </div>

        </div>
      </div>

      <div class="modal-footer py-2">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

<style>
    /* CSS Modal Scroll & Pagination (YG LAMA) */
    #modalDetailArtikel .modal-dialog { max-height: 90vh; }
    #modalDetailArtikel .modal-body { overflow-y: auto; max-height: 75vh; }
    #paginationLinks .pagination { margin-bottom: 0; }
    #paginationLinks .page-link { cursor: pointer; }

    /* --- TAMBAHAN CSS BARU UNTUK MOBILE --- */
    /* Pada layar kecil (mobile), ubah kolom aksi menjadi flex column */
    @media (max-width: 767.98px) {
        /* Target kolom yang memiliki class 'aksi-col' */
        #tabelartikel .aksi-col {
            display: flex !important;
            flex-direction: column; /* Susun ke bawah */
            gap: 4px;               /* Jarak antar tombol */
            align-items: center;    /* Posisikan di tengah, JANGAN stretch */
        }

        /* Hapus margin kanan dan atur ukuran tombol */
        #tabelartikel .aksi-col .btn,
        #tabelartikel .aksi-col a {
            margin-right: 0 !important;
            margin-bottom: 0;

            /* Bikin tombol jadi kotak kecil (Square) */
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important; /* Hapus padding bawaan bootstrap */

            /* Pastikan ikon di tengah tombol */
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/artikel.js') }}"></script>
@endsection