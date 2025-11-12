@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    <div id="alert-area"></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header & Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center flex-wrap">
            <form action="{{ route('admin.pengeluaran.index') }}" method="GET" class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari transaksi..." value="{{ request('search') }}">
            </form>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button type="button" class="btn btn-outline-secondary btn-custom-padding d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                <i class="bi bi-tags me-2"></i> Kelola Kategori
            </button>
            
            <button type="button" class="btn btn-danger btn-custom-padding d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahPengeluaran">
                <i class="bi bi-plus-circle me-2"></i> Tambah Pengeluaran
            </button>
        </div>
    </div>

    {{-- Total --}}
    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pengeluaran</h5>
        <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h5>
    </div>

    {{-- Tabel Utama --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 20%;">Kategori</th>
                            <th>Deskripsi</th> 
                            <th style="width: 20%;" class="text-end">Jumlah</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyPengeluaran">
                        @forelse ($pengeluaran as $index => $item)
                        <tr>
                            <td>{{ $pengeluaran->firstItem() + $index }}</td>
                            <td class="col-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $item->kategoriPengeluaran->nama_kategori_pengeluaran ?? '-' }}</span></td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-end text-danger fw-bold col-nowrap">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="text-center col-nowrap">
                                <a href="{{ route('admin.pengeluaran.edit', $item->id_pengeluaran) }}" class="btn btn-sm me-1"><i class="bi bi-pencil text-primary fs-6"></i></a>
                                <form action="{{ route('admin.pengeluaran.destroy', $item->id_pengeluaran) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm"><i class="bi bi-trash text-danger fs-6"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pengeluaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $pengeluaran->links() }}</div>
        </div>
    </div>
</div>

{{-- MODAL 1: TAMBAH PENGELUARAN --}}
<div class="modal fade" id="modalTambahPengeluaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Pengeluaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPengeluaranAjax" action="{{ route('admin.pengeluaran.store') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori_pengeluaran" id="selectKategoriPengeluaran" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori_pengeluaran }}">{{ $kat->nama_kategori_pengeluaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Tanpa titik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="btnSimpanPengeluaran">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: KELOLA KATEGORI --}}
<div class="modal fade" id="modalKelolaKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Kelola Kategori Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-2">Tambah Kategori Baru</h6>
                        <form id="formKategoriAjax" action="{{ route('admin.kategori-pengeluaran.store') }}" class="d-flex gap-2">
                            <input type="text" name="nama_kategori_pengeluaran" class="form-control" placeholder="Nama kategori..." required>
                            <button type="submit" class="btn btn-primary text-nowrap"><i class="bi bi-plus"></i> Tambah</button>
                        </form>
                    </div>
                </div>

                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
                    <table class="table table-bordered table-hover table-sm align-middle mb-0">
                        <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 10;">
                            <tr>
                                <th class="ps-3">Nama Kategori</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyKategori">
                            @foreach($kategori as $k)
                            <tr>
                                <td class="ps-3">{{ $k->nama_kategori_pengeluaran }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.kategori-pengeluaran.destroy', $k->id_kategori_pengeluaran) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted mt-2 d-block">* Scroll ke bawah untuk melihat kategori lainnya.</small>

            </div>
        </div>
    </div>
</div>

{{-- Script JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/pengeluaran.js') }}"></script>

@endsection