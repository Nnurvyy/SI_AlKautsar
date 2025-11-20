@extends('layouts.app')

@section('title', 'Manajemen Kajian')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Filter Status --}}
            <div>
                <select class="form-select shadow-sm" id="statusFilter" style="width: 160px;">
                    <option value="aktif" selected>Waktu: Aktif</option>
                    <option value="tidak_aktif">Waktu: Lewat</option>
                    <option value="semua">Waktu: Semua</option>
                </select>
            </div>

            {{-- Filter Jenis --}}
            <div>
                <select class="form-select border-primary fw-bold shadow-sm" id="jenisFilter" style="width: 160px;">
                    <option value="semua" selected>Jenis: Semua</option>
                    <option value="event">Khusus Event</option>
                    <option value="harian">Khusus Harian</option>
                </select>
            </div>

            {{-- Search --}}
            <div class="input-group search-bar shadow-sm" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari penceramah...">
            </div>
        </div>
        
        {{-- Tombol Tambah --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKajian">
                <i class="bi bi-plus-circle me-2"></i> Tambah Kajian
            </button>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKajian">
                    <thead class="table-light">
                        <tr> 
                            <th class="text-center">No</th>
                            <th class="text-center">Foto</th>
                            <th>Nama Penceramah</th>
                            <th>Tema Kajian</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Waktu</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo">Loading...</span>
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
          <h5 class="modal-title fw-bold">Form Kajian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
          <div class="mb-3">
            <label class="form-label">Foto Penceramah</label>
            <input type="file" class="form-control" id="foto_penceramah" name="foto_penceramah" accept="image/*">
            <div class="mt-2 text-center">
                <img id="previewFoto" class="d-none rounded border shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
            </div>
          </div>

          {{-- INPUT JENIS KAJIAN (WAJIB) --}}
          <div class="mb-3 p-3 border rounded bg-light">
            <label class="form-label fw-bold mb-2">Jenis Kajian <span class="text-danger">*</span></label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="jenis_kajian" id="jenis_harian" value="harian" checked>
                    <label class="form-check-label" for="jenis_harian">Rutin / Harian</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="jenis_kajian" id="jenis_event" value="event">
                    <label class="form-check-label" for="jenis_event">Event Besar</label>
                </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Nama Penceramah <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_penceramah" name="nama_penceramah" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tema Kajian <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="tema_kajian" name="tema_kajian" required>
          </div>
          <div class="row">
            <div class="col-md-7">
                <div class="mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_kajian" name="tanggal_kajian" required>
                </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                    <label class="form-label">Waktu (WIB)</label>
                    <input type="time" class="form-control" id="waktu_kajian" name="waktu_kajian">
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kajian.js') }}?v=99"></script>
@endsection