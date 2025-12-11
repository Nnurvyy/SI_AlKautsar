<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 0;
            font-size: 14px;
        }

        .info {
            margin-bottom: 20px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-container {
            width: 100%;
            overflow: hidden;
        }

        .summary {
            float: right;
            width: 40%;
            margin-top: 10px;
        }

        .summary th,
        .summary td {
            border: 1px solid #ddd;
            padding: 5px;
        }

        .summary th {
            background-color: #fff;
            text-align: left;
        }

        h3 {
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>Smart Masjid ({{ $settings->nama_masjid }})</p>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
    </div>

    <div class="info">
        <strong>Filter Laporan:</strong> {{ $periodeTeks }}
    </div>

    {{-- Tabel Pemasukan --}}
    @if ($tipe == 'pemasukan' || $tipe == 'semua')
        <h3>Data Pemasukan</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%">Tanggal</th>
                    <th style="width: 20%">Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-end" style="width: 20%">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pemasukanData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->kategori->nama_kategori_keuangan ?? '-' }}</td>
                        <td>{{ $item->deskripsi ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data pemasukan pada periode ini.</td>
                    </tr>
                @endforelse
                {{-- Subtotal Pemasukan jika mau ditampilkan per tabel --}}
                @if ($pemasukanData->count() > 0)
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total Pemasukan</strong></td>
                        <td class="text-end"><strong>Rp
                                {{ number_format($pemasukanData->sum('nominal'), 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- Tabel Pengeluaran --}}
    @if ($tipe == 'pengeluaran' || $tipe == 'semua')
        <h3>Data Pengeluaran</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%">Tanggal</th>
                    <th style="width: 20%">Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-end" style="width: 20%">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengeluaranData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->kategori->nama_kategori_keuangan ?? '-' }}</td>
                        <td>{{ $item->deskripsi ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data pengeluaran pada periode ini.</td>
                    </tr>
                @endforelse
                {{-- Subtotal Pengeluaran --}}
                @if ($pengeluaranData->count() > 0)
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total Pengeluaran</strong></td>
                        <td class="text-end"><strong>Rp
                                {{ number_format($pengeluaranData->sum('nominal'), 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- Ringkasan Akhir --}}
    <div class="summary-container">
        <table class="summary">
            <tr>
                <th>Total Pemasukan</th>
                <td class="text-end text-success" style="color: green;">Rp
                    {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Pengeluaran</th>
                <td class="text-end text-danger" style="color: red;">(Rp
                    {{ number_format($totalPengeluaran, 0, ',', '.') }})</td>
            </tr>
            <tr>
                <th style="background-color: #eee;"><strong>Saldo Akhir</strong></th>
                <td class="text-end" style="background-color: #eee;"><strong>Rp
                        {{ number_format($saldo, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

</body>

</html>
