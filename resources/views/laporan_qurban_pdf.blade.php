<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
            text-align: left;
        }
        h2 {
            text-align: center;
        }
        .total {
            font-weight: bold;
            font-size: 14px;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>{{ $judulLaporan }}</h2>
<p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>

<table class="table">
    <thead>
    <tr>
        <th>No</th>
        <th>Tanggal Setor</th>
        <th>Nama Penyetor</th>
        <th>Hewan Qurban</th>
        <th>Nominal</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($pemasukan as $index => $setoran)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($setoran->tanggal)->translatedFormat('d F Y') }}</td>
            <td>{{ $setoran->tabunganHewanQurban->pengguna->nama ?? 'N/A' }}</td>
            <td>{{ $setoran->tabunganHewanQurban->nama_hewan ?? 'N/A' }}</td>
            <td>Rp {{ number_format($setoran->nominal, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align: center;">Tidak ada data pemasukan pada periode ini.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="total">
    Total Pemasukan: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
</div>

</body>
</html>
