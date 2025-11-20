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
            <a href="{{ route('admin.artikel.create') }}" class="btn btn-primary d-flex align-items-center">
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
                            <th scope="col" style="width: 30%;" class="text-center">Judul Artikel</th>
                            <th scope="col" style="width: 15%;" class="text-center">Penulis Artikel</th>
                            <th scope="col" style="width: 10%;" class="text-center">Status Artikel</th>
                            <th scope="col" style="width: 20%;" class="text-center">Tanggal Terbit</th>
                            <th scope="col" style="width: 13%;" class="text-center">Aksi</th>
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

<!-- Modal Detail Artikel -->
<div class="modal fade" id="modalDetailArtikel" tabindex="-1" aria-labelledby="detailArtikelLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header py-2">
        <h5 class="modal-title fw-bold">Detail Artikel: <span id="detailJudulArtikel"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row">

          <!-- Foto -->
          <div class="col-md-4 text-center mb-3">
              <h6>Foto Artikel</h6>
              <img id="detailFotoArtikel" src="#" class="img-fluid rounded shadow-sm"
                   style="max-height: 250px; object-fit: cover;">
          </div>

          <!-- Tabel Data -->
          <div class="col-md-8">
              <table class="table table-sm table-striped table-bordered">
                  <tbody>
                      <tr><th>Judul</th><td id="d_judul"></td></tr>
                      <tr><th>Penulis</th><td id="d_penulis"></td></tr>
                      <tr><th>Status</th><td id="d_status_artikel"></td></tr>
                      <tr><th>Tanggal Terbit</th><td id="d_tanggal_terbit"></td></tr>
                  </tbody>
              </table>
          </div>

          <!-- Isi Artikel -->
          <div class="col-12 mt-3">
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

/* CSS Modal Scroll */
#modalKhotib .modal-dialog { max-height: 80vh; display: flex; flex-direction: column; }
#modalKhotib .modal-content { height: 100%; display: flex; flex-direction: column; }
#modalKhotib .modal-body { overflow-y: auto; max-height: 70vh; }
#modalKhotib .modal-footer { position: sticky; bottom: 0; background: white; z-index: 2; border-top: 1px solid #dee2e6; }

/* CSS Tombol 'x' di field */
#clearFile:hover { color: #212529; }
#clearFile:focus { box-shadow: none; }

/* CSS untuk Pagination */
#paginationLinks .pagination {
    margin-bottom: 0;
}
#paginationLinks .page-item.disabled .page-link {
    background-color: #e9ecef;
}
#paginationLinks .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
#paginationLinks .page-link {
    cursor: pointer;
}
</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/artikel.js') }}"></script>
@endsection
