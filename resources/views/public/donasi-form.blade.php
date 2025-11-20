@extends('layouts.public')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold">{{ $program->judul }}</h3>
    <p>{{ $program->deskripsi }}</p>

    <div class="card p-4">
        <form action="{{ route('donasi.store') }}" method="POST">
            @csrf

            <input type="hidden" name="program_id" value="{{ $program->id }}">

            <label class="form-label">Nama Donatur</label>
            <input type="text" name="nama_donatur" class="form-control" required>

            <label class="form-label mt-3">Nominal Donasi</label>
            <input type="number" name="jumlah" class="form-control" required>

            <button class="btn btn-primary mt-4 w-100">Kirim Donasi</button>
        </form>
    </div>
</div>
@endsection
