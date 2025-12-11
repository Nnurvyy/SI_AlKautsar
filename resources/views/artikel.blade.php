@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid p-4">

        {{-- HEADER & SEARCH --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- Filter Status --}}
                <select class="form-select rounded-pill ps-3" id="statusFilter" style="width: 160px; border-color: #e5e7eb;">
                    <option value="all" selected>Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>

                {{-- Search Bar --}}
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i
                            class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill"
                        placeholder="Cari judul artikel...">
                </div>
            </div>

            {{-- Tombol Tambah --}}
            <a href="{{ route('pengurus.artikel.create') }}" class="btn btn-gradient-green rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tulis Artikel
            </a>
        </div>

        {{-- CARD TOTAL ARTIKEL --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Total Artikel</h5>
                    <small class="text-muted">Jumlah artikel yang ditulis</small>
                </div>
                <h3 class="fw-bold text-success mb-0">
                    {{ number_format($totalArtikel, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        {{-- TABEL UTAMA --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelartikel">
                        <thead class="bg-light">
                            <tr style="height: 50px;">
                                <th class="text-center ps-4 rounded-top-left" style="width: 5%;">No</th>
                                <th class="text-center" style="width: 15%;">Foto</th>
                                <th style="width: 30%;">Judul Artikel</th>
                                <th style="width: 15%;">Penulis</th>
                                <th class="text-center" style="width: 10%;">Status</th>
                                <th class="text-center" style="width: 10%;">Terbit</th>
                                <th class="text-center pe-4 rounded-top-right" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            {{-- Data dimuat lewat JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="card-footer bg-white border-0 py-3">
                <div id="paginationContainer" class="d-flex justify-content-between align-items-center">
                    <span id="paginationInfo" class="text-muted small ms-2"></span>
                    <nav id="paginationLinks" class="me-2"></nav>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL ARTIKEL --}}
    <div class="modal fade" id="modalDetailArtikel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg modal-rounded">

                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">Preview Artikel</h5>
                        <p class="text-muted small mb-0">Detail konten dan informasi publikasi</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Foto (Kiri) --}}
                        <div class="col-md-4">
                            {{-- UPDATE: Rasio 16:9 --}}
                            <div
                                style="width: 100%; aspect-ratio: 16/9; overflow: hidden; border-radius: 12px; background: #f0f0f0;">
                                <img id="detailFotoArtikel" src="" class="w-100 h-100 shadow-sm"
                                    style="object-fit: cover;">
                            </div>

                            <div class="mt-3 p-3 bg-light rounded-3 border">
                                <table class="table table-sm table-borderless mb-0 small">
                                    <tr>
                                        <td class="text-muted fw-bold">Penulis</td>
                                        <td class="text-end fw-bold text-dark" id="d_penulis"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-bold">Status</td>
                                        <td class="text-end" id="d_status_artikel"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-bold">Tanggal</td>
                                        <td class="text-end text-dark" id="d_tanggal_terbit"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Konten (Kanan) --}}
                        <div class="col-md-8">
                            <h3 id="detailJudulArtikel" class="fw-bold text-dark mb-3"
                                style="font-family: 'Poppins', sans-serif;"></h3>
                            <hr class="text-muted opacity-25">
                            <div id="d_isi" class="article-content text-dark" style="line-height: 1.8;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        #tabelartikel,
        .card,
        .modal-content {
            font-family: 'Poppins', sans-serif;
        }

        .modal-rounded {
            border-radius: 20px !important;
            overflow: hidden;
        }

        .btn-gradient-green {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            color: white;
            transition: all 0.3s;
        }

        .btn-gradient-green:hover {
            background: linear-gradient(135deg, #157347, #198754);
            transform: translateY(-1px);
            color: white;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .rounded-top-left {
            border-top-left-radius: 10px;
        }

        .rounded-top-right {
            border-top-right-radius: 10px;
        }

        #paginationLinks .pagination {
            margin-bottom: 0;
        }

        #paginationLinks .page-link {
            cursor: pointer;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Script Index Artikel --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableBody = document.querySelector("#tabelartikel tbody");
            if (!tableBody) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const statusFilter = document.getElementById("statusFilter");
            const searchInput = document.getElementById("searchInput");
            const paginationLinks = document.getElementById("paginationLinks");
            const paginationInfo = document.getElementById("paginationInfo");
            const modalDetailEl = document.getElementById('modalDetailArtikel');
            const modalDetail = new bootstrap.Modal(modalDetailEl);

            loadArtikel();

            function formatTanggalIndo(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '-';
                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }).format(date);
            }

            function loadArtikel(page = 1) {
                const status = statusFilter ? statusFilter.value : 'all';
                const search = searchInput ? searchInput.value : '';
                let colCount = tableBody.closest('table').querySelector('thead tr').cells.length;
                tableBody.innerHTML =
                    `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success" role="status"></div></td></tr>`;

                fetch(`/pengurus/artikel-data?page=${page}&status=${status}&search=${search}`)
                    .then(res => res.json()).then(res => renderData(res)).catch(err => console.error(err));
            }

            function renderData(res) {
                const tableBody = document.querySelector("#tabelartikel tbody");
                const paginationInfo = document.getElementById("paginationInfo");
                const paginationLinks = document.getElementById("paginationLinks");
                tableBody.innerHTML = "";
                let no = res.from;

                if (res.data.length === 0) {
                    let colCount = tableBody.closest('table').querySelector('thead tr').cells.length;
                    tableBody.innerHTML =
                        `<tr><td colspan="${colCount}" class="text-center py-5 text-muted">Belum ada data artikel.</td></tr>`;
                    paginationInfo.textContent = "";
                    paginationLinks.innerHTML = "";
                    return;
                }

                res.data.forEach(item => {
                    let statusBadge = item.status_artikel === 'published' ?
                        '<span class="badge rounded-pill bg-success px-3">Published</span>' :
                        '<span class="badge rounded-pill bg-secondary px-3">Draft</span>';


                    let foto = item.foto_url ?
                        `<div style="width: 80px; aspect-ratio: 16/9; overflow: hidden; border-radius: 6px; margin: 0 auto;">
                     <img src="${item.foto_url}" style="width: 100%; height: 100%; object-fit: cover;">
                   </div>` :
                        `<div style="width: 80px; aspect-ratio: 16/9; background: #f8f9fa; border-radius: 6px; display:flex; align-items:center; justify-content:center; margin: 0 auto; border: 1px solid #dee2e6;">
                     <i class="bi bi-image text-muted"></i>
                   </div>`;

                    tableBody.innerHTML += `
                <tr>
                    <td class="text-center fw-bold text-muted">${no++}</td>
                    <td class="text-center">${foto}</td>
                    <td><div class="fw-bold text-dark">${item.judul_artikel}</div><small class="text-muted d-block text-truncate" style="max-width: 200px;">${item.penulis_artikel}</small></td>
                    <td>${item.penulis_artikel}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center small">${formatTanggalIndo(item.tanggal_terbit_artikel)}</td>
                    <td class="text-center">                    
                        <div class="d-flex justify-content-center gap-2"> 
                            <button onclick="lihatDetailArtikel('${item.id_artikel}')" class="btn btn-sm btn-info text-white rounded-3 shadow-sm" title="Lihat Detail"><i class="bi bi-eye"></i></button>
                            <a href="/pengurus/artikel/${item.id_artikel}/edit" class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                            <button onclick="hapusArtikel('${item.id_artikel}')" class="btn btn-sm btn-danger rounded-3 shadow-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
                });

                paginationInfo.textContent = `Menampilkan ${res.from || 0} - ${res.to || 0} dari ${res.total} data`;
                renderPagination(res);
            }

            function renderPagination(res) {
                let html = `<ul class="pagination pagination-sm mb-0">`;
                if (res.prev_page_url) html +=
                    `<li class="page-item"><button class="page-link text-success" data-page="${res.current_page - 1}"><i class="bi bi-chevron-left"></i></button></li>`;
                if (res.next_page_url) html +=
                    `<li class="page-item"><button class="page-link text-success" data-page="${res.current_page + 1}"><i class="bi bi-chevron-right"></i></button></li>`;
                html += `</ul>`;
                paginationLinks.innerHTML = html;
                paginationLinks.querySelectorAll(".page-link").forEach(link => {
                    link.addEventListener("click", function() {
                        loadArtikel(this.getAttribute("data-page"));
                    });
                });
            }


            window.lihatDetailArtikel = function(id) {
                fetch(`/pengurus/artikel-data/${id}`)
                    .then(res => {
                        if (!res.ok) throw new Error("Gagal");
                        return res.json();
                    })
                    .then(data => {
                        document.getElementById("detailJudulArtikel").innerText = data.judul_artikel;
                        document.getElementById("d_penulis").innerText = data.penulis_artikel;
                        document.getElementById("d_status_artikel").innerText = data.status_artikel ===
                            'published' ? 'Published' : 'Draft';
                        document.getElementById("d_tanggal_terbit").innerText = formatTanggalIndo(data
                            .tanggal_terbit_artikel);
                        document.getElementById("d_isi").innerHTML = data.isi_artikel;

                        const imgEl = document.getElementById("detailFotoArtikel");
                        if (data.foto_url) {
                            imgEl.src = data.foto_url;
                            imgEl.parentElement.classList.remove('d-none');
                        } else {
                            imgEl.parentElement.classList.add('d-none');
                        }
                        modalDetail.show();
                    })
                    .catch(err => Swal.fire('Error', err.message, 'error'));
            }

            window.hapusArtikel = async function(id_artikel) {
                const confirm = await Swal.fire({
                    title: 'Hapus Artikel?',
                    text: "Data tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Hapus!'
                });
                if (!confirm.isConfirmed) return;
                try {
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');
                    const res = await fetch(`/pengurus/artikel/${id_artikel}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const data = await res.json();
                    if (res.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadArtikel();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (err) {
                    Swal.fire('Gagal', err.message, 'error');
                }
            };

            if (statusFilter) statusFilter.addEventListener("change", () => loadArtikel());
            if (searchInput) searchInput.addEventListener("keyup", () => loadArtikel());
        });
    </script>
@endsection
