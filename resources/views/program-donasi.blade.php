{{-- resources/views/program-donasi.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Program Donasi')

@section('content')
@php
    // Jika layout belum menyertakan meta csrf, tambahkan (biasanya ada di layouts)
@endphp

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Manajemen Program Donasi</h4>
        <button class="btn btn-success" onclick="window.addForm()">
            <i class="bi bi-plus-circle"></i> Tambah Program
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelDonasi" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="12%" class="text-center">Gambar</th>
                            <th>Judul Program</th>
                            <th class="text-end">Target Dana</th>
                            <th class="text-end">Dana Terkumpul</th>
                            <th class="text-center">Persentase</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Memuat data...</td>
                        </tr>
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

{{-- Modal Form (Add / Edit) --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="donasiForm" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" id="id" name="id">

            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Form Program Donasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Program <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="judul" name="judul" required>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="target_dana" class="form-label">Target Dana <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="target_dana" name="target_dana" required min="0" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dana_terkumpul" class="form-label">Dana Terkumpul <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="dana_terkumpul" name="dana_terkumpul" required min="0" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                </div>

                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar Program</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                    <div id="gambar-preview" class="mt-2"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- pastikan layout tidak menambahkan tanda markdown pada src -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/program-donasi.js') }}"></script>
@endpush
