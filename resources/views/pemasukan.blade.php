@extends('layouts.app')

@section('title', 'Pemasukan')

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

    {{-- Header & Tombol (Pemasukan = HIJAU/SUCCESS) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center flex-wrap">
            <form action="{{ route('admin.pemasukan.index') }}" method="GET" class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari transaksi..." value="{{ request('search') }}">
            </form>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button type="button" class="btn btn-outline-secondary btn-custom-padding d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                <i class="bi bi-tags me-2"></i> Kelola Kategori
            </button>
            
            <button type="button" class="btn btn-success btn-custom-padding d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahPemasukan">
                <i class="bi bi-plus-circle me-2"></i> Tambah Pemasukan
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pemasukan</h5>
        <h5 class="fw-bold mb-0 text-custom-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
    </div>

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
                            <th style="width: 15%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyPemasukan">
                        @forelse ($pemasukan as $index => $item)
                        <tr>
                            <td>{{ $pemasukan->firstItem() + $index }}</td>
                            <td class="col-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <td><span class="badge bg-info text-dark">{{ $item->kategoriPemasukan->nama_kategori_pemasukan ?? '-' }}</span></td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-end text-custom-green fw-bold col-nowrap">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="text-center col-nowrap">
                                
                                {{-- LOGIKA TOMBOL MATA: HANYA DONASI --}}
                                @php
                                    $namaKategori = strtolower($item->kategoriPemasukan->nama_kategori_pemasukan ?? '');
                                @endphp

                                @if(str_contains($namaKategori, 'donasi'))
                                    <button class="btn btn-sm btn-info text-white me-1 btn-detail-pemasukan" 
                                        data-id="{{ $item->id_pemasukan }}"
                                        title="Lihat Rincian Donatur">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                @else
                                    {{-- Spacer biar tombol Edit gak geser --}}
                                    <span style="display:inline-block; width: 32px;"></span>
                                @endif

                                <a href="{{ route('admin.pemasukan.edit', $item->id_pemasukan) }}" class="btn btn-sm me-1"><i class="bi bi-pencil text-primary fs-6"></i></a>
                                
                                <form action="{{ route('admin.pemasukan.destroy', $item->id_pemasukan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm"><i class="bi bi-trash text-danger fs-6"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pemasukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $pemasukan->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

{{-- MODAL DETAIL (Tidak berubah) --}}
<div class="modal fade" id="modalDetailPemasukan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Rincian Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4 mt-2">
                    <div class="col-6">
                        <small class="text-muted fw-bold text-uppercase">Kategori</small>
                        <h5 class="fw-bold text-dark mt-1" id="detailKategori">-</h5>
                        <small class="text-muted d-block" id="detailTanggal">-</small>
                    </div>
                    <div class="col-6 border-start text-end">
                        <small class="text-muted fw-bold text-uppercase">Total Masuk</small>
                        <h3 class="fw-bold text-custom-green mt-1" id="detailNominal">Rp 0</h3>
                    </div>
                </div>
                <h6 class="fw-bold mb-3">Daftar Donatur / Sumber Dana</h6>
                <div class="table-responsive border rounded" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Donatur</th>
                                <th>Keterangan</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyRincian">
                            <tr><td colspan="4" class="text-center py-3">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahPemasukan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Pemasukan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPemasukanAjax" action="{{ route('admin.pemasukan.store') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori_pemasukan" id="selectKategoriPemasukan" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori_pemasukan }}">{{ $kat->nama_kategori_pemasukan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Contoh: 100000" required>
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
                    <button type="submit" class="btn btn-success" id="btnSimpanPemasukan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL KELOLA KATEGORI --}}
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
                                <td class="ps-3">{{ $k->nama_kategori_pemasukan }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.kategori-pemasukan.destroy', $k->id_kategori_pemasukan) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/pemasukan.js') }}"></script>

<script>
$(document).ready(function() {
    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    };

    $('body').on('click', '.btn-detail-pemasukan', function() {
        let id = $(this).data('id');
        let url = "{{ route('admin.pemasukan.index') }}/" + id; 

        $('#detailKategori').text('...');
        $('#detailNominal').text('...');
        $('#detailTanggal').text('...');
        $('#tableBodyRincian').html('<tr><td colspan="4" class="text-center py-3">Memuat rincian...</td></tr>');
        
        $('#modalDetailPemasukan').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if(response.status == 'success') {
                    let d = response.data;
                    
                    $('#detailKategori').text(d.kategori);
                    $('#detailNominal').text(formatRupiah(d.nominal));
                    $('#detailTanggal').text(d.tanggal);

                    let rows = '';
                    if (d.donatur && d.donatur.length > 0) {
                        d.donatur.forEach(function(item, index) {
                            let nama = item.nama_donatur || item.nama || 'Hamba Allah';
                            let ket = item.catatan || item.deskripsi || '-';
                            let nom = item.nominal || 0;

                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${nama}</td>
                                    <td>${ket}</td>
                                    <td class="text-end fw-bold text-success">${formatRupiah(nom)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        rows = `
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Tidak ada rincian donatur spesifik untuk data ini. <br>
                                    <small class="text-secondary">(Mungkin ini pemasukan manual/kas umum atau tanggal donasi berbeda)</small>
                                </td>
                            </tr>`;
                    }
                    $('#tableBodyRincian').html(rows);
                }
            },
            error: function() {
                alert('Gagal mengambil data detail.');
            }
        });
    });
});
</script>

@endsection