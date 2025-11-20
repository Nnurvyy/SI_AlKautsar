<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Pemasukan Tabungan Qurban</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 0; font-size: 14px; }
        .info { margin-bottom: 20px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .summary { float: right; width: 350px; margin-top: 20px; }
        .summary th, .summary td { border: none; padding: 5px 8px; }
        .summary th { text-align: left; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Pemasukan Tabungan Qurban</h1>
    <p>E-Masjid (Al-Kautsar 561)</p>
    <p>Dicetak pada: {{ $tanggalCetak }}</p>
</div>

<div class="info">
    <strong>Filter Laporan:</strong> {{ $periodeTeks }}
</div>

<table>
    <thead>
    <tr>
        <th style="width: 5%;" class="text-center">No</th>
        <th style="width: 15%;">Tanggal</th>
        <th style="width: 30%;">Nama Penyetor</th>
        <th style="width: 20%;">Hewan Qurban</th>
        <th class="text-end">Nominal Setoran</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($pemasukanData as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
            {{-- GANTI: jamaah->name --}}
            <td>{{ $item->tabunganHewanQurban->jamaah->name ?? 'N/A' }}</td>
            <td>{{ Str::ucfirst($item->tabunganHewanQurban->nama_hewan ?? 'N/A') }}</td>
            <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">Tidak ada data pemasukan pada periode ini.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<table class="summary">
    <tr>
        <th>Total Pemasukan:</th>
        <td class="text-end"><strong>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong></td>
    </tr>
</table>

</body>
</html>
