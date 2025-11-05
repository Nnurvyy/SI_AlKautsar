<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 0; font-size: 14px; }
        .info { margin-bottom: 20px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .summary { float: right; width: 300px; margin-top: 20px; }
        .summary th, .summary td { border: none; padding: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>E-Masjid (Al-Kautsar 561)</p>
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
    </div>

    <div class="info">
        <strong>Filter Laporan:</strong> {{ $periodeTeks }}
    </div>

    @if($tipe == 'pemasukan' || $tipe == 'semua')
        <h3>Data Pemasukan</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pemasukanData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->deskripsi }}</td>
                        <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada data pemasukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($tipe == 'pengeluaran' || $tipe == 'semua')
        <h3>Data Pengeluaran</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengeluaranData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->deskripsi }}</td>
                        <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada data pengeluaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <table class="summary">
        <tr>
            <th>Total Pemasukan:</th>
            <td class="text-end">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Pengeluaran:</th>
            <td class="text-end">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Saldo Akhir:</th>
            <td class="text-end"><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

</body>
</html>