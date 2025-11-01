@extends('layouts.app')

@section('title', 'Infaq Jumat')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
        
        {{-- Search Bar (Kiri) --}}
        <div class="input-group search-bar me-2" style="max-width: 350px;"> 
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Tanggal....">
        </div>
        
        {{-- Tombol Tambah Khutbah (Kanan) --}}
        <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modaltambahinfaq">
            <i class="bi bi-plus-circle me-2"></i>
            Tambah Pemasukan Infaq
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Infaq Jumat</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKhotib">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 10%;" class="text-center">Tanggal</th>
                            <th scope="col">Nominal</th>
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

<!-- Modal Form -->
<div class="modal fade" id="modaltambahinfaq" tabindex="-1" aria-labelledby="modalInfaqLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTambahInfaq">
        
        <input type="hidden" id="id_infaq" name="id_infaq">

        <div class="modal-header">
          <h5 class="modal-title" id="modalInfaqLabel">Tambah Infaq Jumat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
          {{-- Input Tanggal --}}
          <div class="mb-3">
            <label for="tanggal_infaq" class="form-label">Tanggal Infaq <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="tanggal_infaq" name="tanggal_infaq" required>
          </div>
          
          {{-- Input Nominal --}}
          <div class="mb-3">
            <label for="nominal_infaq" class="form-label">Nominal Infaq (Rp) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="nominal_infaq" name="nominal_infaq" placeholder="Contoh: 1500000" min="0" required>
            <div class="form-text">Masukkan nominal tanpa titik atau koma.</div>
          </div>
          
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
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
<script src="{{ asset('js/khotib.js') }}"></script>
@endsection
