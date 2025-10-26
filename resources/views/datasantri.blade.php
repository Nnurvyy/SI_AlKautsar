@extends('layouts.app')

<!-- 1. Judul Halaman diubah -->
@section('title', 'Manajemen Data Santri')

@section('content')

<!-- 2. Data Dummy diubah menjadi data transaksi -->
@php
    $data = [
        (object)[
            'nis' => '132948291',
            'nama_santri' => 'papang rahmawan',
            'kelas' => '7A',
            'no_telp' => '081234567890',
            'wali_santri' => 'Budi',
            'status' => true
        ],
        (object)[
            'nis' => '302817245',
            'nama_santri' => 'kipli kurniawan',
            'kelas' => '8B',
            'no_telp' => '082233445566',
            'wali_santri' => 'Siti',
            'status' => false
        ],
        (object)[
            'nis' => '312132123',
            'nama_santri' => 'lathif abdul abdel',
            'kelas' => '9C',
            'no_telp' => '083344556677',
            'wali_santri' => 'Slamet',
            'status' => true
        ],
    ];
@endphp

<div class="container-fluid p-4">

    <!-- 3. Header Atas diubah (Search, Filter, Tombol) -->
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        
        <!-- Search Bar & Filter -->
        <div class="d-flex align-items-center flex-wrap">
            <!-- Search Bar -->
            <div class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Cari Data Santri (NIS atau nama)...">
            </div>
        </div>
        <!--Tombol Aksi -->
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <a href="#" 
            class="btn btn-primary btn-custom-padding d-flex align-items-center"
            data-bs-toggle="modal"
            data-bs-target="#modalTambahDataSantri">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Data Santri
            </a>
        </div>
    </div>

    <!-- 5. Tabel Transaksi -->
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    
                    <!-- 6. Header Tabel diubah -->
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 10%">NIS</th>
                            <th scope="col" style="width: 25%">Nama</th>
                            <th scope="col" style="width: 8%">Kelas</th>
                            <th scope="col" style="width: 17%">No.Telepon</th>
                            <th scope="col" style="width: 13%">Wali</th>
                            <th scope="col" style="width: 12%">Status</th>
                            <th scope="col" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    
                    <!-- 7. Isi Tabel diubah -->
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->nis}}</td>
                            <td>{{ $item->nama_santri}}</td>
                            <td>{{ $item->kelas}}</td>
                            <td>{{ $item->no_telp}}</td>
                            <td>{{ $item->wali_santri}}</td>
                            <td class="text-left">
                                @if ($item->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            
                            <td class="text-center col-nowrap">
                                <!-- Tombol Edit -->
                                <a href="#" 
                                class="btn btn-sm btnEditSantri me-1" 
                                title="Edit"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEditDataSantri">
                                    <i class="bi bi-pencil text-primary fs-6"></i>
                                </a>

                                <!-- Tombol Hapus -->
                                <a href="#" 
                                class="btn btn-sm" 
                                title="Hapus"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalHapusDataSantri">
                                    <i class="bi bi-trash text-danger fs-6"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Santri -->
<div class="modal fade" id="modalTambahDataSantri" tabindex="-1" aria-labelledby="modalEditSantriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalEditSantriLabel">Edit Data Santri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditSantri" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="row g-3">

                        <input type="hidden" name="id_students" id="edit_id">

                        <div class="col-md-6">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control" name="nis" id="edit_nis">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_santri" id="edit_nama">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control" name="kelas" id="edit_kelas">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telepon Santri</label>
                            <input type="text" class="form-control" name="no_telepon" id="edit_telp">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Wali Santri</label>
                            <input type="text" class="form-control" name="wali_santri" id="edit_wali">
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" name="is_aktif" id="edit_status">
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
                </div>

            </form>

        </div>
    </div>
</div>



<!-- modal untuk edit data santru -->
<div class="modal fade" id="modalEditDataSantri" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Santri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Isi form di sini...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- modal untuk hapus data santru -->
<div class="modal fade" id="modalHapusDataSantri" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Data Santri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Isi form di sini...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Event Klik Edit
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {

            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nis').value = this.dataset.nis;
            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_kelas').value = this.dataset.kelas;
            document.getElementById('edit_telp').value = this.dataset.telp;
            document.getElementById('edit_wali').value = this.dataset.wali;
            
            document.getElementById('edit_status').checked = (this.dataset.status == '1');

            // Update action form
            document.getElementById('formEditSantri').action = "/students/update/" + this.dataset.id;

        });
    });

});
</script>
@endpush

