<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Pemasukan Tabungan Qurban</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; }
        .info { margin-bottom: 10px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #444; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-uppercase { text-transform: uppercase; }
        .text-muted { color: #666; }
        
        /* Summary Box */
        .summary-container { width: 100%; text-align: right; margin-top: 10px; }
        .summary-table { width: auto; display: inline-table; border: 1px solid #333; }
        .summary-table td { border: none; padding: 5px 10px; font-size: 12px; }
        .summary-table .label { background-color: #f0f0f0; font-weight: bold; }
        .summary-table .value { font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Pemasukan Tabungan Qurban</h1>
    <p>{{ $settings->nama_masjid ?? 'Masjid Besar' }}</p>
    <p>{{ $settings->alamat ?? 'Alamat Masjid' }}</p>
    <p>Dicetak pada: {{ $tanggalCetak }}</p>
</div>

<div class="info">
    <table style="width: 100%; border: none; margin: 0;">
        <tr style="border: none;">
            <td style="border: none; padding: 0; width: 50%;"><strong>Filter:</strong> {{ $periodeTeks }}</td>
            <td style="border: none; padding: 0; text-align: right;"><strong>Status:</strong> Lunas / Terbayar (Success)</td>
        </tr>
    </table>
</div>

<table>
    <thead>
    <tr>
        <th style="width: 5%;">No</th>
        <th style="width: 12%;">Tanggal</th>
        <th style="width: 20%;">Penyetor</th>
        <th style="width: 33%;">Rincian Hewan</th>
        <th style="width: 12%;">Metode</th> <th style="width: 18%;">Nominal</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($pemasukanData as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center">
                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
            </td>
            <td>
                {{-- Nama Jamaah --}}
                <strong style="font-size: 11px;">
                    {{ $item->tabunganHewanQurban->jamaah->name ?? 'Jamaah Terhapus' }}
                </strong>
                <br>
                {{-- ID Tabungan --}}
                <span class="text-muted" style="font-size: 9px;">
                    ID: {{ substr($item->tabunganHewanQurban->id_tabungan_hewan_qurban, 0, 8) }}
                </span>
            </td>
            <td>
                {{-- Rincian Hewan --}}
                @if($item->tabunganHewanQurban && $item->tabunganHewanQurban->details->count() > 0)
                    <ul style="margin: 0; padding-left: 12px;">
                        @foreach($item->tabunganHewanQurban->details as $detail)
                            <li>
                                {{ $detail->jumlah_hewan }} Ekor 
                                {{ ucfirst($detail->hewan->nama_hewan ?? '-') }}
                                <span style="font-size: 9px;">({{ ucfirst($detail->hewan->kategori_hewan ?? '') }})</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span style="color: red;">-</span>
                @endif
            </td>
            <td class="text-center">
                {{-- Info Metode Pembayaran & Ref --}}
                <div class="text-uppercase" style="font-weight: bold; font-size: 9px;">
                    {{ $item->metode_pembayaran ?? 'TUNAI' }}
                </div>
                @if($item->order_id)
                    <div class="text-muted" style="font-size: 8px;">
                        Ref: {{ substr($item->order_id, -8) }}
                    </div>
                @endif
            </td>
            <td class="text-end">
                Rp {{ number_format($item->nominal, 0, ',', '.') }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center" style="padding: 20px;">
                Tidak ada data pemasukan pada periode ini.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- Total Summary --}}
<div class="summary-container">
    <table class="summary-table">
        <tr>
            <td class="label">Total Pemasukan:</td>
            <td class="value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

</body>
</html>