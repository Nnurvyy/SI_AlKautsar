@extends('layouts.app')

@section('title', 'Pemasukan')

@section('content')
<div class="container-fluid p-4">
    {{-- 🔍 Bagian Search & Filter --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center flex-wrap">
            <div class="input-group me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Cari transaksi...">
            </div>

            <select class="form-select me-2" style="width: auto;">
                <option selected>Semua Kategori</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>

            <select class="form-select" style="width: auto;">
                <option selected>Semua Divisi</option>
                @foreach ($divisi as $div)
                    <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tombol Aksi --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button type="button" 
                    class="btn btn-outline-secondary d-flex align-items-center me-2"
                    data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                <i class="bi bi-tags me-2"></i> Kelola Kategori
            </button>

            {{-- Tombol Tambah --}}
            <button type="button" 
                    class="btn btn-success d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalTambahPemasukan">
                <i class="bi bi-plus-circle me-2"></i> Tambah Pemasukan
            </button>
        </div>
    </div>

    {{-- 💰 Total Pemasukan --}}
    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pemasukan</h5>
        <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
    </div>

    {{-- 📋 Tabel Data --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Divisi</th>
                            <th>Santri</th>
                            <th>Deskripsi</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center" style="width:10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pemasukan as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') }}</td>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $item->divisi->nama_divisi ?? '-' }}</td>
                            <td>{{ $item->siswa->nama ?? '-' }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>{{ $item->metode_pembayaran }}</td>
                            <td class="text-end text-success fw-bold">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditPemasukan{{ $item->id_pemasukan }}">
                                    <i class="bi bi-pencil text-primary fs-5"></i>
                                </button>
                                <form action="{{ route('pemasukan.destroy', $item->id_pemasukan) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm" 
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash text-danger fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- 🔧 Modal Edit --}}
                        <div class="modal fade" id="modalEditPemasukan{{ $item->id_pemasukan }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('pemasukan.update', $item->id_pemasukan) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pemasukan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label>Tanggal</label>
                                                    <input type="date" name="tanggal_transaksi" class="form-control" 
                                                           value="{{ $item->tanggal_transaksi }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Kategori</label>
                                                    <select name="id_pemasukan_kategori" class="form-select">
                                                        @foreach ($kategori as $kat)
                                                            <option value="{{ $kat->id }}" 
                                                                {{ $item->id_pemasukan_kategori == $kat->id ? 'selected' : '' }}>
                                                                {{ $kat->nama_kategori }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Divisi</label>
                                                    <select name="id_divisi" class="form-select">
                                                        @foreach ($divisi as $div)
                                                            <option value="{{ $div->id }}" 
                                                                {{ $item->id_divisi == $div->id ? 'selected' : '' }}>
                                                                {{ $div->nama_divisi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Santri</label>
                                                    <select name="id_students" class="form-select">
                                                        @foreach ($siswa as $s)
                                                            <option value="{{ $s->id }}" 
                                                                {{ $item->id_students == $s->id ? 'selected' : '' }}>
                                                                {{ $s->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Metode Pembayaran</label>
                                                    <input type="text" name="metode_pembayaran" class="form-control" 
                                                           value="{{ $item->metode_pembayaran }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Nominal</label>
                                                    <input type="number" name="nominal" class="form-control" 
                                                           value="{{ $item->nominal }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Deskripsi</label>
                                                    <input type="text" name="deskripsi" class="form-control" 
                                                           value="{{ $item->deskripsi }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pemasukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ➕ Modal Tambah --}}
<div class="modal fade" id="modalTambahPemasukan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('pemasukan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pemasukan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal_transaksi" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Kategori</label>
                            <select name="id_pemasukan_kategori" class="form-select" required>
                                @foreach ($kategori as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Divisi</label>
                            <select name="id_divisi" class="form-select" required>
                                @foreach ($divisi as $div)
                                    <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Santri</label>
                            <select name="id_students" class="form-select" required>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Metode Pembayaran</label>
                            <input type="text" name="metode_pembayaran" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Nominal</label>
                            <input type="number" name="nominal" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Deskripsi</label>
                            <input type="text" name="deskripsi" class="form-control">
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


@endsection
