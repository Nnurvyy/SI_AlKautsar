@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="container p-4">
    <div class="card border-0 shadow-sm col-md-8 mx-auto">
        <div class="card-body">
            <h4 class="mb-4 fw-bold">Tambah Kategori Pemasukan</h4>

            <form action="{{ route('admin.kategori-pemasukan.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori_pemasukan" class="form-control @error('nama_kategori_pemasukan') is-invalid @enderror" placeholder="Contoh: Infaq, Donasi, SPP" value="{{ old('nama_kategori_pemasukan') }}" required>
                    @error('nama_kategori_pemasukan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('admin.kategori-pemasukan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection