@extends('layouts.app')

@section('title', 'Khutbah Jumat')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. Load CSS Cropper.js --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <div class="container-fluid p-4">

        {{-- Header: Filter, Search, Tombol --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

            <div class="d-flex flex-wrap align-items-center">
                <div class="me-2 mb-2 mb-md-0">
                    <select class="form-select" id="statusFilter" style="width: 150px;">
                        <option value="aktif" selected>Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="semua">Semua</option>
                    </select>
                </div>

                {{-- Search Bar --}}
                <div class="input-group search-bar me-2 mb-2 mb-md-0" style="width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0"
                        placeholder="Cari khotib, imam, tema, atau tanggal...">
                </div>
            </div>

            {{-- Tombol Tambah Khotib --}}
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <button class="btn btn-primary d-flex align-items-center" id="btnTambahModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Khutbah
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
            <h5 class="fw-bold mb-0">Data Khutbah Jumat</h5>
        </div>

        {{-- Wrapper Tabel --}}
        <div class="card transaction-table border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabelKhotib">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 5%;" class="text-center">No</th>
                                <th scope="col" style="width: 10%;" class="text-center">Foto</th>
                                <th scope="col">Nama Khotib</th>
                                <th scope="col">Nama Imam</th>
                                <th scope="col">Tema Khutbah</th>
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

    <div class="modal fade" id="modalKhotib" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Jadwal Khutbah</h5>
                        <p class="text-muted mb-0 small" style="line-height: 1;">Kelola data petugas jumat</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formKhotib" enctype="multipart/form-data"
                    style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
                    <input type="hidden" id="id_khutbah" name="id_khutbah">

                    <div class="modal-body">
                        <div class="form-wrapper">
                            <div class="form-section-title">
                                Isi Data Lengkap
                            </div>

                            <div class="mb-3">
                                <label for="foto_khotib" class="form-label">Foto Khotib (Max 2 Mb)</label>
                                <input type="file" class="d-none" id="foto_khotib" name="foto_khotib" accept="image/*">

                                <div class="position-relative custom-file-wrapper mb-2">
                                    <label for="foto_khotib" id="foto_khotib_label"
                                        class="form-control d-block text-truncate border cursor-pointer m-0"
                                        style="cursor: pointer;">
                                        <span class="text-muted"><i class="bi bi-cloud-upload me-2"></i>Pilih foto...</span>
                                    </label>
                                    <button type="button" class="btn position-absolute d-none" id="clearFile"
                                        title="Hapus foto"
                                        style="top: 50%; right: 0.5rem; transform: translateY(-50%); z-index: 5; color: #dc3545; background: transparent; border: 0;">
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                </div>

                                <div id="previewContainer" class="position-relative d-none mt-3">
                                    <img id="previewFoto" class="img-fluid" alt="Preview">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_khotib" class="form-label">Nama Khotib <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_khotib" name="nama_khotib"
                                    placeholder="Contoh: Ust. Fulan">
                            </div>

                            <div class="mb-3">
                                <label for="nama_imam" class="form-label">Nama Imam <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_imam" name="nama_imam"
                                    placeholder="Nama Imam Sholat">
                            </div>

                            <div class="mb-3">
                                <label for="tema_khutbah" class="form-label">Tema Khutbah <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tema_khutbah" name="tema_khutbah"
                                    placeholder="Judul/Tema materi">
                            </div>

                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal Pelaksanaan <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal">
                            </div>

                            <div>
                                <button type="submit" class="btn-action-primary shadow-sm">
                                    Simpan Data <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Potong Gambar (Rasio 1:1)</h5>
                    <button type="button" class="btn-close" id="btnCloseCrop" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="img-container"
                        style="height: 500px; background-color: #333; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                        <img id="imageToCrop" style="max-width: 100%; display: block;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancelCrop">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnCropImage">
                        <i class="bi bi-scissors me-2"></i>Potong & Gunakan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS Custom --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        #modalKhotib,
        #modalCrop {
            font-family: 'Poppins', sans-serif;
        }


        #modalKhotib .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #modalKhotib .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 25px;
            background: white;
            z-index: 10;
            flex-shrink: 0;
        }

        #modalKhotib .modal-body {
            overflow-y: auto;
            padding: 20px 25px;
            flex-grow: 1;
        }

        .form-wrapper {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 15px;
            padding: 20px;
        }

        .form-section-title {
            color: #166534;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }

        #previewFoto {
            width: 100%;
            height: auto;
            max-height: 350px;
            object-fit: contain;
            border-radius: 12px;
            display: block;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }


        .btn-action-primary {
            background-color: #198754;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            width: 100%;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
        }

        .btn-action-primary:hover {
            background-color: #157347;
            color: white;
        }

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

    {{-- 4. Load JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Script Cropper JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {


            const token = document.querySelector('meta[name="csrf-token"]').content;


            const modalKhotibElement = document.getElementById('modalKhotib');
            const modalKhotib = new bootstrap.Modal(modalKhotibElement);
            const form = document.getElementById('formKhotib');
            const btnTambahModal = document.getElementById('btnTambahModal');


            const modalCropElement = document.getElementById('modalCrop');
            const modalCrop = new bootstrap.Modal(modalCropElement);
            const imageToCrop = document.getElementById('imageToCrop');
            const btnCropImage = document.getElementById('btnCropImage');
            const btnCancelCrop = document.getElementById('btnCancelCrop');
            const btnCloseCrop = document.getElementById('btnCloseCrop');


            const fotoInput = document.getElementById('foto_khotib');
            const fotoLabel = document.getElementById('foto_khotib_label');
            const fotoLabelSpan = fotoLabel.querySelector('span');
            const clearFileBtn = document.getElementById('clearFile');
            const preview = document.getElementById('previewFoto');
            const previewContainer = document.getElementById('previewContainer');


            const tbody = document.querySelector('#tabelKhotib tbody');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const paginationContainer = document.getElementById('paginationLinks');
            const paginationInfo = document.getElementById('paginationInfo');
            const sortTanggal = document.getElementById('sortTanggal');
            const sortIcon = document.getElementById('sortIcon');
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;


            let state = {
                currentPage: 1,
                status: 'aktif',
                search: '',
                perPage: 10,
                sortBy: 'tanggal',
                sortDir: 'desc',
                searchTimeout: null
            };


            let cropper = null;
            let croppedBlob = null;
            let originalFileName = '';




            if (fotoInput) {
                fotoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {

                        if (!file.type.startsWith('image/')) {
                            Swal.fire('Error', 'File harus berupa gambar', 'error');
                            this.value = '';
                            return;
                        }

                        originalFileName = file.name;
                        const reader = new FileReader();

                        reader.onload = function(evt) {

                            imageToCrop.src = evt.target.result;


                            modalKhotib.hide();
                            modalCrop.show();
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }


            modalCropElement.addEventListener('shown.bs.modal', () => {

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1 / 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                });
            });


            btnCropImage.addEventListener('click', () => {
                if (!cropper) return;


                cropper.getCroppedCanvas({
                    width: 800,
                    height: 800
                }).toBlob((blob) => {


                    croppedBlob = blob;


                    const url = URL.createObjectURL(blob);
                    preview.src = url;
                    previewContainer.classList.remove('d-none');


                    fotoLabelSpan.textContent = "Gambar (1:1) Siap Upload";
                    fotoLabelSpan.classList.remove('text-muted');
                    clearFileBtn.classList.remove('d-none');


                    closeCropModal();
                    modalKhotib.show();

                }, 'image/jpeg', 0.9);
            });


            const handleCancelCrop = () => {
                fotoInput.value = '';
                closeCropModal();
                modalKhotib.show();
            };

            btnCancelCrop.addEventListener('click', handleCancelCrop);
            btnCloseCrop.addEventListener('click', handleCancelCrop);

            function closeCropModal() {
                modalCrop.hide();
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }


            form.addEventListener('submit', async e => {
                e.preventDefault();
                setLoading(true);

                const id = document.getElementById('id_khutbah').value;
                const formData = new FormData(form);


                if (croppedBlob) {
                    const fname = originalFileName || 'cropped.jpg';
                    formData.set('foto_khotib', croppedBlob, fname);
                }

                const url = id ? `/pengurus/khotib-jumat/${id}` : '/pengurus/khotib-jumat';
                if (id) formData.append('_method', 'PUT');

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok) {
                        Swal.fire('Berhasil!', data.message, 'success');
                        modalKhotib.hide();
                        loadKhotib();
                    } else {
                        if (res.status === 422 && data.errors) {
                            let msg = Object.values(data.errors).map(e => e[0]).join('<br>');
                            throw new Error(msg);
                        }
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                } catch (err) {
                    Swal.fire('Gagal', err.message, 'error');
                } finally {
                    setLoading(false);
                }
            });




            if (btnTambahModal) {
                btnTambahModal.addEventListener('click', () => {
                    document.getElementById('id_khutbah').value = '';
                    form.reset();
                    resetFileState();
                    modalKhotib.show();
                });
            }


            modalKhotibElement.addEventListener('hidden.bs.modal', function() {
                if (!document.querySelector('.modal-backdrop')) {


                }
            });

            function resetFileState() {
                croppedBlob = null;
                originalFileName = '';
                fotoInput.value = '';
                preview.src = '';
                previewContainer.classList.add('d-none');
                clearFileBtn.classList.add('d-none');
                fotoLabelSpan.textContent = "Pilih foto...";
                fotoLabelSpan.classList.add('text-muted');
            }


            clearFileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                resetFileState();
            });

            function setLoading(isLoading) {
                if (isLoading) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;
                } else {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            }



            window.editKhotib = async function(id) {
                try {
                    const res = await fetch(`/pengurus/khotib-jumat/${id}`);
                    if (!res.ok) throw new Error('Data error');
                    const data = await res.json();

                    document.getElementById('id_khutbah').value = data.id_khutbah;
                    document.getElementById('nama_khotib').value = data.nama_khotib;
                    document.getElementById('nama_imam').value = data.nama_imam;
                    document.getElementById('tema_khutbah').value = data.tema_khutbah;
                    if (data.tanggal) document.getElementById('tanggal').value = data.tanggal.split('T')[0];


                    croppedBlob = null;

                    if (data.foto_khotib) {
                        fotoLabelSpan.textContent = data.foto_khotib.split('/').pop();
                        fotoLabelSpan.classList.remove('text-muted');
                        clearFileBtn.classList.remove('d-none');
                        preview.src = data.foto_url;
                        previewContainer.classList.remove('d-none');
                    } else {
                        resetFileState();
                    }

                    modalKhotib.show();
                } catch (err) {
                    Swal.fire('Gagal', err.message, 'error');
                }
            }

            window.hapusKhotib = async function(id) {
                const c = await Swal.fire({
                    title: 'Hapus data?',
                    text: "Data tidak bisa kembali!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus'
                });
                if (!c.isConfirmed) return;

                try {
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');
                    const res = await fetch(`/pengurus/khotib-jumat/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: fd
                    });
                    if (res.ok) {
                        Swal.fire('Terhapus', '', 'success');
                        loadKhotib();
                    } else {
                        throw new Error('Gagal menghapus');
                    }
                } catch (e) {
                    Swal.fire('Error', e.message, 'error');
                }
            }


            async function loadKhotib() {
                let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
                tbody.innerHTML =
                    `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

                const url =
                    `/pengurus/khotib-jumat-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

                try {
                    const res = await fetch(url);
                    const response = await res.json();
                    renderTable(response.data, response.from || 1);
                    renderPagination(response);
                } catch (err) {
                    tbody.innerHTML =
                        `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
                }
            }

            function renderTable(data, startNum) {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>`;
                    return;
                }
                data.forEach((item, i) => {
                    const row = `
            <tr>
                <td class="text-center">${startNum + i}</td>
                <td class="text-center"><img src="${item.foto_url}" class="rounded" style="width:50px;height:50px;object-fit:cover;"></td>
                <td>${item.nama_khotib}</td>
                <td>${item.nama_imam}</td>
                <td>${item.tema_khutbah}</td> 
                <td class="text-center">${item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" onclick="editKhotib('${item.id_khutbah}')"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="hapusKhotib('${item.id_khutbah}')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            }

            function renderPagination(response) {
                paginationInfo.textContent =
                    `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
                let linksHtml = '<ul class="pagination justify-content-center mb-0">';
                response.links.forEach(link => {
                    let label = link.label.replace('&laquo; Previous', '<').replace('Next &raquo;', '>');
                    let active = link.active ? 'active' : '';
                    let disabled = !link.url ? 'disabled' : '';
                    linksHtml +=
                        `<li class="page-item ${active} ${disabled}"><a class="page-link" href="${link.url}" data-page="${link.url}">${label}</a></li>`;
                });
                linksHtml += '</ul>';
                paginationContainer.innerHTML = linksHtml;
            }


            searchInput.addEventListener('keyup', () => {
                clearTimeout(state.searchTimeout);
                state.searchTimeout = setTimeout(() => {
                    state.search = searchInput.value;
                    state.currentPage = 1;
                    loadKhotib();
                }, 300);
            });
            statusFilter.addEventListener('change', () => {
                state.status = statusFilter.value;
                state.currentPage = 1;
                loadKhotib();
            });
            if (sortTanggal) sortTanggal.addEventListener('click', () => {
                state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
                sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
                loadKhotib();
            });
            paginationContainer.addEventListener('click', e => {
                e.preventDefault();
                if (e.target.classList.contains('page-link') && e.target.href) {
                    state.currentPage = new URL(e.target.href).searchParams.get('page');
                    loadKhotib();
                }
            });


            loadKhotib();
        });
    </script>
@endsection
