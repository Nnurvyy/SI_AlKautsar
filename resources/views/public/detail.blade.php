@extends('layout')

@section('content')
    <div class="container py-5">
        <h2>{{ $program->judul }}</h2>

        <img src="{{ asset('uploads/donasi/' . $program->gambar) }}" width="300">

        <p>{{ $program->deskripsi }}</p>

        <h4>Form Donasi</h4>

        <form action="{{ route('donasi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">

            <label>Nama:</label>
            <input type="text" name="nama" class="form-control">

            <label>Nominal:</label>
            <input type="number" name="nominal" class="form-control">

            <label>Metode Pembayaran:</label>
            <select name="metode" class="form-control">
                <option>Transfer Bank</option>
                <option>QRIS</option>
                <option>COD Ke Masjid</option>
            </select>

            <label>Pesan (opsional):</label>
            <textarea name="pesan" class="form-control"></textarea>

            <button type="submit" class="btn btn-primary mt-3">Kirim Donasi</button>
        </form>
    </div>
@endsection
