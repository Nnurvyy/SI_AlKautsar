@extends('layouts.app')

@section('title', 'Edit Pemasukan')

@section('content')
<div class="container p-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h4 class="mb-4 fw-bold">Edit Pemasukan</h4>

            {{-- Form Update --}}
            <form action="{{ route('admin.pemasukan.update', $pemasukan->id_pemasukan) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Kategori Pemasukan --}}
                <div class="mb-3">
                    <label class="form-label">Kategori Pemasukan</label>
                    <select name="id_kategori_pemasukan" class="form-select @error('id_kategori_pemasukan') is-invalid @enderror" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->id_kategori_pemasukan }}" 
                                {{ old('id_kategori_pemasukan', $pemasukan->id_kategori_pemasukan) == $k->id_kategori_pemasukan ? 'selected' : '' }}>
                                {{ $k->nama_kategori_pemasukan }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kategori_pemasukan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nominal --}}
                <div class="mb-3">
                    <label class="form-label">Nominal (Rp)</label>
                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" 
                           value="{{ old('nominal', $pemasukan->nominal) }}" required>
                    @error('nominal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $pemasukan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                           value="{{ old('tanggal', $pemasukan->tanggal) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                    <a href="{{ route('admin.pemasukan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection