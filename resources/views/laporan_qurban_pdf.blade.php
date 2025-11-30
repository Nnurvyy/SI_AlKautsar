<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Pemasukan Tabungan Qurban</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        .info { margin-bottom: 15px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #444; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .summary { float: right; width: 300px; margin-top: 10px; }
        .summary th, .summary td { border: none; padding: 5px; }
        .summary th { text-align: left; }
        .badge { font-size: 10px; padding: 2px 4px; border-radius: 3px; background: #eee; border: 1px solid #ccc; margin-right: 3px; display: inline-block; margin-bottom: 2px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Pemasukan Tabungan Qurban</h1>
    <p>{{ $settings->nama_masjid }}</p>
    <p>Dicetak pada: {{ $tanggalCetak }}</p>
</div>

<div class="info">
    <strong>Filter Laporan:</strong> {{ $periodeTeks }}
</div>

<table>
    <thead>
    <tr>
        <th style="width: 5%;">No</th>
        <th style="width: 12%;">Tanggal</th>
        <th style="width: 25%;">Nama Penyetor</th>
        <th style="width: 40%;">Rincian Hewan Qurban</th>
        <th style="width: 18%;">Nominal Setoran</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($pemasukanData as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
            <td>
                <strong>{{ $item->tabunganHewanQurban->jamaah->name ?? 'Jamaah Terhapus' }}</strong>
                <br>
                <span style="font-size: 10px; color: #666;">ID: {{ substr($item->tabunganHewanQurban->id_tabungan_hewan_qurban, 0, 8) }}</span>
            </td>
            <td>
                {{-- Loop Detail Hewan karena relasi One to Many --}}
                @if($item->tabunganHewanQurban->details->count() > 0)
                    @foreach($item->tabunganHewanQurban->details as $detail)
                        <div style="margin-bottom: 2px;">
                            • {{ $detail->jumlah_hewan }} Ekor 
                            {{ ucfirst($detail->hewan->nama_hewan ?? '-') }} 
                            ({{ ucfirst($detail->hewan->kategori_hewan ?? '-') }})
                        </div>
                    @endforeach
                @else
                    <span style="color: red;">Data Hewan Tidak Ditemukan</span>
                @endif
            </td>
            <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data pemasukan pada periode ini.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<table class="summary">
    <tr>
        <th style="font-size: 14px;">Total Pemasukan:</th>
        <td class="text-end" style="font-size: 14px;"><strong>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong></td>
    </tr>
</table>

</body>
</html>