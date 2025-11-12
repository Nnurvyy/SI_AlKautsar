@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="container p-4">
    <div class="card border-0 shadow-sm col-md-8 mx-auto">
        <div class="card-body">
            <h4 class="mb-4 fw-bold">Edit Kategori Pemasukan</h4>

            <form action="{{ route('admin.kategori-pemasukan.update', $kategori->id_kategori_pemasukan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori_pemasukan" class="form-control @error('nama_kategori_pemasukan') is-invalid @enderror" value="{{ old('nama_kategori_pemasukan', $kategori->nama_kategori_pemasukan) }}" required>
                    @error('nama_kategori_pemasukan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                    <a href="{{ route('admin.kategori-pemasukan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection