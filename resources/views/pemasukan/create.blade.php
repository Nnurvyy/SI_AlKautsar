@extends('layouts.app')

@section('title', 'Tambah Pemasukan')

@section('content')
<div class="container p-4">
    <h4 class="mb-4 fw-bold">Tambah Pemasukan</h4>

    <form action="{{ route('pemasukan.store') }}" method="POST">
        @csrf

        {{-- Kategori Pemasukan --}}
        <div class="mb-3">
            <label class="form-label">Kategori Pemasukan</label>
            <select name="id_kategori_pemasukan" class="form-select" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->id_kategori_pemasukan }}">{{ $k->nama_kategori_pemasukan }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nominal --}}
        <div class="mb-3">
            <label class="form-label">Nominal</label>
            <input type="number" name="nominal" class="form-control" placeholder="Masukkan jumlah pemasukan" required>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Opsional"></textarea>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        {{-- Tombol --}}
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('pemasukan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
