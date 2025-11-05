@extends('layouts.app')

@section('title', 'Inventaris & Stock')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
        
        {{-- Search Bar (Kiri) --}}
        <div class="input-group search-bar me-2" style="max-width: 350px;"> 
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari nama barang....">
        </div>
        
        {{-- Tombol Tambah Khutbah (Kanan) --}}
        <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalInventaris">
            <i class="bi bi-plus-circle me-2"></i>
            Tambah Barang Inventaris
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Barang Inventaris dan Stock</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKhotib">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 30%;" class="text-center">Nama Barang</th>
                            <th scope="col" style="width: 10%;" class="text-center">Satuan</th>
                            <th scope="col" style="width: 15%;" class="text-center">Kondisi</th>
                            <th scope="col" style="width: 10%;" class="text-center">Stock</th>
                            <th scope="col" style="width: 30%;" class="text-center">Aksi</th>
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

<div class="modal fade" id="modalInventaris" tabindex="-1" aria-labelledby="modalInventarisLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formInventarisStock">
        
        <input type="hidden" id="id_barang" name="id_barang">

        <div class="modal-header">
          <h5 class="modal-title" id="modalInventarisLabel">Tambah/Ubah Data Inventaris</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
          {{-- Input Nama Barang --}}
          <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
          </div>
          
          {{-- Dropdown Satuan --}}
          <div class="mb-3">
            <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
            <select class="form-select" id="satuan" name="satuan" required>
                <option value="" disabled selected>Pilih Satuan</option>
                <option value="Pcs">Pcs</option>
                <option value="Unit">Unit</option>
                <option value="Set">Set</option>
                <option value="Meter">Meter</option>
                <option value="Roll">Roll</option>
            </select>
          </div>

          {{-- Dropdown Kondisi --}}
          <div class="mb-3">
            <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
            <select class="form-select" id="kondisi" name="kondisi" required>
                <option value="" disabled selected>Pilih Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                <option value="Rusak Berat">Rusak Berat</option>
            </select>
          </div>

          {{-- Input Stock --}}
          <div class="mb-3">
            <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="stock" name="stock" placeholder="Masukkan Jumlah Stok" min="0" required>
          </div>
          
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Data</button>
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
<script src="{{ asset('js/inventaris.js') }}"></script>
@endsection
