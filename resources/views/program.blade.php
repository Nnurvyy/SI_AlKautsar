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
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari Nama Program...">
            </div>
        </div>
        
        {{-- Tombol Tambah --}}
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
                <table class="table table-hover align-middle" id="tabelProgram">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 10%;" class="text-center">Foto</th>
                            <th scope="col" style="width: 20%;" class="text-center">Nama Program</th>
                            <th scope="col" style="width: 15%;" class="text-center">Tanggal</th>
                            <th scope="col" style="width: 15%;" class="text-center">Lokasi</th>
                            <th scope="col" style="width: 15%;" class="text-center">Status</th>
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

<div class="modal fade" id="modalProgram" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="formProgram" enctype="multipart/form-data">
        <input type="hidden" id="id_program" name="id_program">

        <div class="modal-header">
          <h5 class="modal-title fw-bold">Form Data Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label mb-1">Foto Program</label>
            <input type="file" class="d-none" id="foto_program" name="foto_program" accept="image/*">
            
            <div class="position-relative">
                <label for="foto_program" id="foto_program_label" class="form-control d-block text-truncate" style="cursor: pointer;">
                    <span class="text-muted">Pilih foto...</span>
                </label>
                <button type="button" class="btn position-absolute d-none" id="clearFile" title="Hapus foto" 
                        style="top: 50%; right: 0.3rem; transform: translateY(-50%); z-index: 5; padding: 0 0.5rem; font-size: 1.2rem; color: #6c757d; line-height: 1; background: transparent; border: 0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div id="previewContainer" class="position-relative d-none mt-2 text-center">
                <img id="previewFoto" class="rounded mt-2" style="max-width: 100%; max-height: 200px; object-fit: cover;">
            </div>
          </div>

          <div class="mb-3">
            <label for="nama_program" class="form-label mb-1">Nama Program <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_program" name="nama_program" required>
          </div>

          <div class="mb-3">
            <label for="penyelenggara_program" class="form-label mb-1">Penyelenggara <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="penyelenggara_program" name="penyelenggara_program" required>
          </div>

          <div class="mb-3">
            <label for="lokasi_program" class="form-label mb-1">Lokasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="lokasi_program" name="lokasi_program" required>
          </div>

          <div class="mb-3">
            <label for="tanggal_program" class="form-label mb-1">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" id="tanggal_program" name="tanggal_program" required>
          </div>

          <div class="mb-3">
            <label for="status_program" class="form-label">Status Program</label>
                <select class="form-select" id="status_program" name="status_program" required>
                    <option value="" disabled selected>Pilih Status</option>
                    <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                    <option value="sedang berjalan">Sedang Berjalan</option>
                    <option value="sudah dijalankan">Sudah Dijalankan</option>
                </select>
          </div>            

          <div class="mb-1">
            <label for="deskripsi_program" class="form-label mb-1">Deskripsi <span class="text-danger">*</span></label>
            <textarea class="form-control" id="deskripsi_program" name="deskripsi_program" rows="3" required></textarea>
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

<div class="modal fade" id="modalDetailProgram" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Detail Program</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="row">
            <div class="col-md-5 text-center mb-3">
                <img id="detailFotoProgram" src="#" alt="Foto Program" class="img-fluid rounded shadow-sm" style="max-height: 300px; object-fit: cover;">
            </div>
            
            <div class="col-md-7">
                <h4 id="d_nama" class="fw-bold mb-3"></h4>
                <table class="table table-borderless">
                    <tr><td style="width: 140px;" class="fw-bold">Penyelenggara</td><td>: <span id="d_penyelenggara"></span></td></tr>
                    <tr><td class="fw-bold">Lokasi</td><td>: <span id="d_lokasi"></span></td></tr>
                    <tr><td class="fw-bold">Waktu</td><td>: <span id="d_tanggal"></span></td></tr>
                    <tr><td class="fw-bold">Status</td><td>: <span id="d_status"></span></td></tr>
                </table>
                
                <div class="mt-3">
                    <label class="fw-bold">Deskripsi:</label>
                    <p id="d_deskripsi" class="text-muted bg-light p-2 rounded"></p>
                </div>
            </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<style>
/* CSS Modal Scroll */
#modalProgram .modal-dialog { max-height: 80vh; display: flex; flex-direction: column; }
#modalProgram .modal-content { height: 100%; display: flex; flex-direction: column; }
#modalProgram .modal-body { overflow-y: auto; max-height: 70vh; }
#modalProgram .modal-footer { position: sticky; bottom: 0; background: white; z-index: 2; border-top: 1px solid #dee2e6; }

/* CSS Tombol 'x' di field foto */
#clearFile:hover { color: #212529 !important; }
#clearFile:focus { box-shadow: none; }

/* --- CSS MOBILE FIX (Agar tombol aksi menumpuk rapi di HP) --- */
#tabelProgram .badge {
    white-space: normal !important; /* KUNCI: Memaksa teks wrap */
    display: inline-block;          /* Agar padding rapi */
    text-align: center;             /* Teks rata tengah */
    line-height: 1.2;               /* Jarak antar baris diperkecil sedikit */
    padding: 6px 8px;               /* Padding agar badge terlihat kotak rapi */
    min-width: 80px;                /* Opsional: lebar minimal agar enak dilihat */
}

/* CSS Mobile Fix yang tadi (Tombol Aksi) - BIARKAN SEPERTI INI */
@media (max-width: 767.98px) {
    /* Container kolom aksi */
    #tabelProgram .aksi-col {
        display: flex !important;
        flex-direction: column; /* Susun ke bawah */
        gap: 4px;               /* Jarak antar tombol diperkecil */
        align-items: center;    /* Posisikan tombol di tengah-tengah kolom */
    }

    /* Tombol aksi */
    #tabelProgram .aksi-col .btn {
        margin-right: 0 !important;
        margin-bottom: 0;

        /* Bikin tombol jadi kotak kecil (Square) */
        width: 32px !important; 
        height: 32px !important;
        padding: 0 !important;    /* Hapus padding bawaan bootstrap */
        
        /* Pastikan ikon di tengah tombol */
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/program.js') }}"></script>
@endsection