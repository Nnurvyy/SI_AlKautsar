@extends('layouts.app')

@section('title', 'Tambah Pemasukan')

@section('content')
<div class="container p-4">
    <h4 class="mb-4 fw-bold">Tambah Pemasukan</h4>

    <form action="{{ route('pemasukan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Divisi</label>
            <select name="id_divisi" class="form-select" required>
                <option value="">Pilih Divisi</option>
                @foreach($divisi as $d)
                    <option value="{{ $d->id_divisi }}">{{ $d->nama_divisi }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="id_kategori" class="form-select" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Santri (Opsional)</label>
            <select name="id_siswa" class="form-select">
                <option value="">Tidak ada</option>
                @foreach($siswa as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Metode Pembayaran</label>
            <input type="text" name="metode_pembayaran" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nominal</label>
            <input type="number" name="nominal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Transaksi</label>
            <input type="date" name="tanggal_transaksi" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('pemasukan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
