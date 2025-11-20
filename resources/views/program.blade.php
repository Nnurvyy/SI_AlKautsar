@extends('layouts.app')

@section('title', 'Program')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Header: Filter, Search, Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center">
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="all" selected>Semua</option>
                    <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                    <option value="sedang berjalan">Sedang Berjalan</option>
                    <option value="sudah dijalankan">Sudah Dijalankan</option>
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="input-group search-bar me-2 mb-2 mb-md-0" style="width: 350px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari Nama Program atau tanggal...">
            </div>
        </div>
        
        {{-- Tombol Tambah Khotib --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalProgram">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Program
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Program</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKhotib">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 30%" class="text-center">Nama Program</th>
                            <th scope="col" style="width: 15%" class="text-center">Tanggal Program</th>
                            <th scope="col" style="width: 20%" class="text-center">Lokasi Program</th>
                            <th scope="col" style="width: 15%" class="text-center">Status</th> <!-- Kolom Status Baru -->
                            <th scope="col" style="width: 15%" class="text-center">Aksi</th>
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

<!-- Modal Form Program -->
<div class="modal fade" id="modalProgram" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="formProgram" enctype="multipart/form-data">
        <input type="hidden" id="id_program" name="id_program">

        <!-- HEADER -->
        <div class="modal-header py-2">
          <h5 class="modal-title fw-bold">Form Data Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body">

          <!-- File Foto -->
          <div class="mb-3">
            <label class="form-label mb-1">Foto Program</label>
            <input type="file" class="form-control" name="foto_program" id="foto_program" accept="image/*">
            
            <div id="previewContainer" class="mt-2 d-none text-center">
                <img id="previewFoto" src="#" alt="Preview Foto Program" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="clearFile">
                    <i class="bi bi-x-circle-fill"></i> Hapus Foto
                </button>
            </div>
        </div>

          <!-- Nama Program -->
          <div class="mb-3">
            <label for="nama_program" class="form-label mb-1">Nama Program <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_program" name="nama_program">
          </div>

          <!-- Penyelenggara -->
          <div class="mb-3">
            <label for="penyelenggara_program" class="form-label mb-1">Penyelenggara <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="penyelenggara_program" name="penyelenggara_program">
          </div>

          <!-- Lokasi -->
          <div class="mb-3">
            <label for="lokasi_program" class="form-label mb-1">Lokasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="lokasi_program" name="lokasi_program">
          </div>

          <!-- Tanggal -->
          <div class="mb-3">
            <label for="tanggal_program" class="form-label mb-1">Tanggal Pelaksanaan<span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" id="tanggal_program" name="tanggal_program">
          </div>

        <div class="mb-3">
            <label for="status_program" class="form-label">Status Program</label>
                <select class="form-select" id="status_program" name="status_program" required>
                    <option value="" disabled selected>Pilih Status</option>
                    <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                    <option value="sedang berjalan">Sedang Berjalan</option>
                    <option value="sudah dijalankan">Sudah Dijalankan</option>
                </select>
            <div class="invalid-feedback" id="status_program_error"></div>
        </div>            

          <!-- Deskripsi -->
          <div class="mb-1">
            <label for="deskripsi_program" class="form-label mb-1">Deskripsi <span class="text-danger">*</span></label>
            <textarea class="form-control" id="deskripsi_program" name="deskripsi_program" rows="3"></textarea>
          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Program -->
<div class="modal fade" id="modalDetailProgram" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      
      <div class="modal-header py-2">
        <h5 class="modal-title fw-bold" id="detailModalTitle">Detail Program: <span id="detailNamaProgram"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body" id="detailProgramBody">
        <div class="row">
            
            <div class="col-md-4 text-center mb-3">
                <h6>Foto Program</h6>
                <img id="detailFotoProgram" src="#" alt="Foto Program" class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: cover;">
            </div>
            
            <div class="col-md-8">
                <table class="table table-sm table-striped table-bordered">
                    <tbody>
                        <tr><th>Nama Program</th><td id="d_nama"></td></tr>
                        <tr><th>Penyelenggara</th><td id="d_penyelenggara"></td></tr>
                        <tr><th>Lokasi</th><td id="d_lokasi"></td></tr>
                        <tr><th>Tanggal Pelaksanaan</th><td id="d_tanggal"></td></tr>
                        <tr><th>Status</th><td id="d_status"></td></tr>
                    </tbody>
                </table>
            </div>
            
            <div class="col-12 mt-3">
                <h6 class="fw-bold">Deskripsi Lengkap:</h6>
                <div id="d_deskripsi" class="p-3 border rounded bg-light"></div>
            </div>
        </div>
      </div>
      
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
<script src="{{ asset('js/program.js') }}"></script>
@endsection
