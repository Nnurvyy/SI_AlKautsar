@extends('layouts.app')

@section('title', 'Khotib Jumat')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Menggunakan container-fluid dan padding (p-4) seperti di file pengeluaran --}}
<div class="container-fluid p-4">

    {{-- Header: Search bar di kiri, Tombol di kanan --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        {{-- Search Bar --}}
        <div class="input-group search-bar me-2" style="width: 300px;">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari khotib, imam, atau tema...">
        </div>
        
        {{-- Tombol Tambah Khotib (Style baru dengan Ikon) --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalKhotib">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Khotib
            </button>
        </div>
    </div>

    {{-- Wrapper Tabel dengan Card, Shadow, dan Responsive --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                {{-- Mengganti class tabel menjadi table-hover dan align-middle --}}
                <table class="table table-hover align-middle" id="tabelKhotib">
                    
                    {{-- Header tabel dengan style table-light --}}
                    <thead class="table-light">
                        <tr> 
                            <!-- Hanya No, Foto, dan Aksi yang diatur lebarnya -->
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 10%;" class="text-center">Foto</th>
                            
                            <!-- DIUBAH: style="width: 15%" DIHAPUS -->
                            <th scope="col">Nama Khotib</th>
                            <!-- DIUBAH: style="width: 15%" DIHAPUS -->
                            <th scope="col">Nama Imam</th>
                            
                            <th scope="col">Tema Khutbah</th>
                            
                            <!-- DIUBAH: style="width: 10%" DIHAPUS -->
                            <th scope="col" class="text-center">Tanggal</th>
                            
                            <th scope="col" style="width: 8%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data dimuat lewat JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form (Tidak ada perubahan di modal) -->
<div class="modal fade" id="modalKhotib" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formKhotib" enctype="multipart/form-data">
        
        <input type="hidden" id="id_khutbah" name="id_khutbah">

        <div class="modal-header">
          <h5 class="modal-title">Tambah Khotib Jumat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          
          <div class="mb-3">
            <label for="foto_khotib" class="form-label">Foto</label>
            <input type="file" class="d-none" id="foto_khotib" name="foto_khotib" accept="image/*">
            <div class="position-relative">
                <label for="foto_khotib" id="foto_khotib_label" class="form-control d-block text-truncate" style="cursor: pointer;">
                    <span class="text-muted">Choose file...</span>
                </label>
                <button type="button" class="btn position-absolute d-none" id="clearFile" title="Hapus foto" 
                        style="top: 50%; right: 0.3rem; transform: translateY(-50%); z-index: 5; padding: 0 0.5rem; font-size: 1.2rem; color: #6c757d; line-height: 1; background: transparent; border: 0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="previewContainer" class="position-relative d-none mt-2">
                <img id="previewFoto"
                     class="img-fluid rounded"
                     style="max-height: 200px; object-fit: cover; width: 100%;">
            </div>
          </div>

          <div class="mb-3">
            <label for="nama_khotib" class="form-label">Nama Khotib</label>
            <input type="text" class="form-control" id="nama_khotib" name="nama_khotib">
          </div>
          <div class="mb-3">
            <label for="nama_imam" class="form-label">Nama Imam</label>
            <input type="text" class="form-control" id="nama_imam" name="nama_imam">
          </div>
          <div class="mb-3">
            <label for="tema_khutbah" class="form-label">Tema Khutbah</label>
            <input type="text" class="form-control" id="tema_khutbah" name="tema_khutbah">
          </div>
          <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal">
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
/* CSS untuk Search Bar */
.search-bar .input-group-text { background-color: #fff; border-right: 0; }
.search-bar .form-control { border-left: 0; box-shadow: none; }
.search-bar .form-control:focus { border-color: #dee2e6; box-shadow: none; }

/* CSS Modal Scroll */
#modalKhotib .modal-dialog { max-height: 80vh; display: flex; flex-direction: column; }
#modalKhotib .modal-content { height: 100%; display: flex; flex-direction: column; }
#modalKhotib .modal-body { overflow-y: auto; max-height: 70vh; }
#modalKhotib .modal-footer { position: sticky; bottom: 0; background: white; z-index: 2; border-top: 1px solid #dee2e6; }

/* CSS Tombol 'x' di field */
#clearFile:hover { color: #212529; }
#clearFile:focus { box-shadow: none; }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Script utama Anda (SEMUA LOGIKA PINDAH KE SINI) --}}
<script src="{{ asset('js/khotib.js') }}"></script>
@endsection

