@extends('layouts.app')

@section('title', 'Pemasukan')

@section('content')

{{-- Meta CSRF Token (Wajib untuk AJAX) --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Tempat Alert JS muncul --}}
    <div id="alert-area"></div>

    {{-- Alert Session PHP (untuk aksi non-AJAX jika ada) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header & Tombol Aksi --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center flex-wrap">
            <form action="{{ route('admin.pemasukan.index') }}" method="GET" class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari transaksi..." value="{{ request('search') }}">
            </form>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            {{-- TOMBOL MODAL KELOLA KATEGORI --}}
            <button type="button" class="btn btn-outline-secondary btn-custom-padding d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                <i class="bi bi-tags me-2"></i>
                Kelola Kategori
            </button>
            
            {{-- TOMBOL MODAL TAMBAH PEMASUKAN --}}
            <button type="button" class="btn btn-success btn-custom-padding d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahPemasukan">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Pemasukan
            </button>
        </div>
    </div>

    {{-- Card Total --}}
    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pemasukan</h5>
        <h5 class="fw-bold mb-0 text-custom-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
    </div>

    {{-- Tabel Utama Pemasukan --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">No</th>
                            <th scope="col" style="width: 15%;">Tanggal</th>
                            <th scope="col" style="width: 20%;">Kategori</th>
                            <th scope="col">Deskripsi</th> 
                            <th scope="col" style="width: 20%;" class="text-end">Jumlah</th>
                            <th scope="col" style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyPemasukan">
                        @forelse ($pemasukan as $index => $item)
                        <tr>
                            <td>{{ $pemasukan->firstItem() + $index }}</td>
                            <td class="col-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $item->kategoriPemasukan->nama_kategori_pemasukan ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-end text-custom-green fw-bold col-nowrap">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="text-center col-nowrap">
                                <a href="{{ route('admin.pemasukan.edit', $item->id_pemasukan) }}" class="btn btn-sm me-1" title="Edit">
                                    <i class="bi bi-pencil text-primary fs-6"></i>
                                </a>
                                <form action="{{ route('admin.pemasukan.destroy', $item->id_pemasukan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" title="Hapus">
                                        <i class="bi bi-trash text-danger fs-6"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pemasukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pemasukan->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ============================== MODAL SECTION ============================== --}}

<div class="modal fade" id="modalTambahPemasukan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Pemasukan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPemasukanAjax" action="{{ route('admin.pemasukan.store') }}">
                <div class="modal-body">
                    {{-- Kategori --}}
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori_pemasukan" id="selectKategoriPemasukan" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori_pemasukan }}">{{ $kat->nama_kategori_pemasukan }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nominal --}}
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Contoh: 100000" required>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSimpanPemasukan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKelolaKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Kelola Kategori Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-2">Tambah Kategori Baru</h6>
                        <form id="formKategoriAjax" action="{{ route('admin.kategori-pemasukan.store') }}" class="d-flex gap-2">
                            <input type="text" name="nama_kategori_pemasukan" class="form-control" placeholder="Nama kategori baru..." required>
                            <button type="submit" class="btn btn-primary text-nowrap">
                                <i class="bi bi-plus"></i> Tambah
                            </button>
                        </form>
                    </div>
                </div>

                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
                    <table class="table table-bordered table-hover table-sm align-middle mb-0">
                        {{-- Header Sticky dengan Background Warna agar tidak transparan saat discroll --}}
                        <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 10;">
                            <tr>
                                <th class="ps-3">Nama Kategori</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyKategori">
                            @foreach($kategori as $k)
                            <tr>
                                <td class="ps-3">{{ $k->nama_kategori_pemasukan }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.kategori-pemasukan.destroy', $k->id_kategori_pemasukan) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

{{-- LOAD SCRIPT JS --}}
{{-- Pastikan jQuery sudah diload di layout utama, jika belum, uncomment baris di bawah --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Load logic JS Pemasukan --}}
<script src="{{ asset('js/pemasukan.js') }}"></script>

@endsection