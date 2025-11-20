@extends('layouts.app')

@section('title', 'Manajemen Kajian')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Header: Filter, Search, Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Filter Status --}}
            <div>
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="aktif" selected>Status: Aktif</option>
                    <option value="tidak_aktif">Status: Tidak Aktif</option>
                    <option value="semua">Status: Semua</option>
                </select>
            </div>

            {{-- Filter Jenis Kajian (BARU) --}}
            <div>
                <select class="form-select" id="jenisFilter" style="width: 150px;">
                    <option value="semua" selected>Jenis: Semua</option>
                    <option value="event">Kajian Event</option>
                    <option value="harian">Kajian Harian</option>
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="input-group search-bar" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari penceramah...">
            </div>
        </div>
        
        {{-- Tombol Tambah --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalKajian">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Kajian
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Kajian</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKajian">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 10%;" class="text-center">Foto</th>
                            <th scope="col">Nama Penceramah</th>
                            <th scope="col">Tema Kajian</th>
                            <th scope="col" class="text-center">Jenis</th> {{-- Kolom Baru --}}
                            <th scope="col" class="text-center">Waktu</th>
                            <th scope="col" class="text-center" id="sortTanggal" style="cursor:pointer;">
                                Tanggal <i id="sortIcon" class="bi bi-arrow-down"></i>
                            </th>
                            <th scope="col" style="width: 8%;" class="text-center">Aksi</th>
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

{{-- Modal Form --}}
<div class="modal fade" id="modalKajian" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formKajian" enctype="multipart/form-data">
        
        <input type="hidden" id="id_kajian" name="id_kajian">

        <div class="modal-header">
          <h5 class="modal-title">Tambah Kajian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          
          <div class="mb-3">
            <label for="foto_penceramah" class="form-label">Foto Penceramah</label>
            <input type="file" class="d-none" id="foto_penceramah" name="foto_penceramah" accept="image/*">
            <div class="position-relative">
                <label for="foto_penceramah" id="foto_penceramah_label" class="form-control d-block text-truncate" style="cursor: pointer;">
                    <span class="text-muted">Choose file...</span>
                </label>
                <button type="button" class="btn position-absolute d-none" id="clearFile" title="Hapus foto" 
                        style="top: 50%; right: 0.3rem; transform: translateY(-50%); z-index: 5; padding: 0 0.5rem; font-size: 1.2rem; color: #6c757d; line-height: 1; background: transparent; border: 0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="previewContainer" class="position-relative d-none mt-2">
                <img id="previewFoto" class="rounded mt-2 mx-auto d-block" style="width: 200px; height: 200px; object-fit: cover;">
            </div>
          </div>

          {{-- Input Baru: Jenis Kajian --}}
          <div class="mb-3">
            <label class="form-label">Jenis Kajian <span class="text-danger">*</span></label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="jenis_kajian" id="jenis_event" value="event">
                    <label class="form-check-label" for="jenis_event">
                        Event Besar
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="jenis_kajian" id="jenis_harian" value="harian" checked>
                    <label class="form-check-label" for="jenis_harian">
                        Kajian Harian/Rutin
                    </label>
                </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="nama_penceramah" class="form-label">Nama Penceramah <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_penceramah" name="nama_penceramah">
          </div>
          <div class="mb-3">
            <label for="tema_kajian" class="form-label">Tema Kajian <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="tema_kajian" name="tema_kajian">
          </div>
          <div class="row">
            <div class="col-md-7">
                <div class="mb-3">
                    <label for="tanggal_kajian" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_kajian" name="tanggal_kajian">
                </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="waktu_kajian" class="form-label">Waktu (WIB)</label>
                    <input type="time" class="form-control" id="waktu_kajian" name="waktu_kajian">
                </div>
            </div>
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
#modalKajian .modal-dialog { max-height: 80vh; display: flex; flex-direction: column; }
#modalKajian .modal-content { height: 100%; display: flex; flex-direction: column; }
#modalKajian .modal-body { overflow-y: auto; max-height: 70vh; }
#modalKajian .modal-footer { position: sticky; bottom: 0; background: white; z-index: 2; border-top: 1px solid #dee2e6; }
#clearFile:hover { color: #212529; }
#clearFile:focus { box-shadow: none; }
#paginationLinks .pagination { margin-bottom: 0; }
#paginationLinks .page-item.disabled .page-link { background-color: #e9ecef; }
#paginationLinks .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
#paginationLinks .page-link { cursor: pointer; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kajian.js') }}"></script>
@endsection