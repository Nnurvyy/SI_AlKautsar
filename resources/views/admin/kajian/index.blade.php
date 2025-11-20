@extends('layouts.app')

@section('title', 'Manajemen Kajian')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center gap-2">

            {{-- Filter Status --}}
            <select class="form-select shadow-sm" id="statusFilter" style="width: 160px;">
                <option value="semua">Waktu: Semua</option>
                <option value="aktif">Waktu: Aktif</option>
                <option value="tidak_aktif">Waktu: Lewat</option>
            </select>

            {{-- Filter Jenis --}}
            <select class="form-select border-primary fw-bold shadow-sm" id="jenisFilter" style="width: 160px;">
                <option value="semua">Jenis: Semua</option>
                <option value="event">Khusus Event</option>
                <option value="harian">Khusus Harian</option>
            </select>

            {{-- Search --}}
            <div class="input-group search-bar shadow-sm" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari penceramah...">
            </div>

        </div>

        {{-- Tambah --}}
        <button class="btn btn-primary d-flex align-items-center shadow-sm"
            data-bs-toggle="modal" data-bs-target="#modalKajian">
            <i class="bi bi-plus-circle me-2"></i> Tambah Kajian
        </button>
    </div>

    {{-- TABEL --}}
    <div class="card border-0 shadow-sm">
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
                    <tbody id="tbodyKajian">
                        <tr><td colspan="8" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>

            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo" class="text-muted small">Loading...</span>
                <nav id="paginationLinks"></nav>
            </div>

        </div>
    </div>
</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="modalKajian" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="formKajian" enctype="multipart/form-data">

        <input type="hidden" id="id_kajian" name="id_kajian">

        <div class="modal-header">
            <h5 class="modal-title fw-bold">Form Data Kajian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

            {{-- Foto --}}
            <div class="mb-3">
                <label class="form-label">Foto Penceramah</label>
                <input type="file" class="form-control" id="foto_penceramah" name="foto_penceramah">
                <img id="previewFoto" class="d-none mt-2 border rounded" style="width:150px;height:150px;object-fit:cover;">
            </div>

            {{-- Jenis --}}
            <div class="mb-3 p-3 border rounded bg-light">
                <label class="form-label fw-bold mb-2">Jenis Kajian *</label>
                <div class="d-flex gap-3">
                    <label><input type="radio" name="jenis_kajian" value="harian" checked> Harian</label>
                    <label><input type="radio" name="jenis_kajian" value="event"> Event</label>
                </div>
            </div>

            {{-- Input lain --}}
            <input class="form-control mb-2" type="text" id="nama_penceramah" name="nama_penceramah" placeholder="Nama Penceramah">
            <input class="form-control mb-2" type="text" id="tema_kajian" name="tema_kajian" placeholder="Tema Kajian">
            <input class="form-control mb-2" type="date" id="tanggal_kajian" name="tanggal_kajian">
            <input class="form-control mb-2" type="time" id="waktu_kajian" name="waktu_kajian">

        </div>

        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ============================= --}}
{{--  JAVASCRIPT TANPA FILE TAMBAHAN --}}
{{-- ============================= --}}
<script>
let state = {
    page: 1,
    jenis: "semua",
    status: "semua",
    search: ""
};

function loadKajian() {

    let url =
        `/admin/kajian-data?page=${state.page}` +
        `&jenis=${state.jenis}` +
        `&status=${state.status}` +
        `&search=${state.search}`;

    fetch(url)
        .then(r => r.json())
        .then(res => {

            let tbody = "";
            if (res.data.length === 0) {
                tbody = `<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>`;
            } else {
                res.data.forEach((d, i) => {
                    tbody += `
                    <tr>
                        <td class="text-center">${(res.from + i)}</td>
                        <td class="text-center"><img src="/storage/${d.foto_penceramah}" style="width:60px;height:60px;object-fit:cover;" class="rounded"></td>
                        <td>${d.nama_penceramah}</td>
                        <td>${d.tema_kajian}</td>
                        <td class="text-center">${d.jenis_kajian}</td>
                        <td class="text-center">${d.waktu_kajian ?? '-'}</td>
                        <td class="text-center">${d.tanggal_kajian}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" onclick="editKajian(${d.id_kajian})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="hapusKajian(${d.id_kajian})">Hapus</button>
                        </td>
                    </tr>`;
                });
            }

            document.querySelector("#tbodyKajian").innerHTML = tbody;
            document.querySelector("#paginationInfo").innerHTML = `Halaman ${res.current_page} dari ${res.last_page}`;

            renderPagination(res);
        });
}

function renderPagination(res) {
    let html = `<ul class="pagination">`;

    for (let i = 1; i <= res.last_page; i++) {
        html += `
        <li class="page-item ${i === res.current_page ? 'active' : ''}">
            <a class="page-link" href="#" onclick="state.page=${i}; loadKajian(); return false;">${i}</a>
        </li>`;
    }

    html += `</ul>`;
    document.querySelector("#paginationLinks").innerHTML = html;
}

/* EVENT FILTER */
document.getElementById("jenisFilter").addEventListener("change", e => {
    state.jenis = e.target.value;
    state.page = 1;
    loadKajian();
});

document.getElementById("statusFilter").addEventListener("change", e => {
    state.status = e.target.value;
    state.page = 1;
    loadKajian();
});

document.getElementById("searchInput").addEventListener("keyup", e => {
    state.search = e.target.value;
    state.page = 1;
    loadKajian();
});

/* LOAD AWAL */
loadKajian();
</script>

@endsection
