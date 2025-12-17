<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #2c3e50; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; color: #7f8c8d; }

        /* Info Filter */
        .meta-info { margin-bottom: 15px; font-size: 12px; }
        .meta-info span { font-weight: bold; color: #2c3e50; }

        /* Tabel Utama */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        
        th { background-color: #f8f9fa; font-weight: bold; color: #444; text-transform: uppercase; font-size: 10px; }
        
        /* Kolom Spesifik */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .col-date { width: 12%; }
        .col-type { width: 10%; }
        .col-cat { width: 15%; }
        .col-desc { width: auto; } /* Deskripsi fleksibel */
        .col-money { width: 15%; white-space: nowrap; }

        /* Warna Nominal */
        .text-success { color: #27ae60; }
        .text-danger { color: #c0392b; }
        .bg-light-success { background-color: #eafaf1; color: #27ae60; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; display: inline-block; }
        .bg-light-danger { background-color: #fdedec; color: #c0392b; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; display: inline-block; }

        /* Ringkasan Bawah (Card Style) */
        .summary-wrapper { width: 100%; margin-top: 20px; page-break-inside: avoid; }
        .summary-card { float: right; width: 50%; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
        .summary-row { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .summary-row:last-child { border-bottom: none; background-color: #f8f9fa; }
        .summary-label { float: left; font-weight: bold; color: #555; }
        .summary-value { float: right; font-weight: bold; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .summary-container {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        /* Kita gunakan tabel untuk layout ringkasan agar tinggi otomatis menyesuaikan konten */
        .summary-table {
            width: 50%;             /* Lebar kartu 50% */
            margin-left: auto;      /* Geser ke kanan (float right replacement) */
            border: 1px solid #ddd;
            border-collapse: collapse;
            font-size: 11px;
        }

        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
            background-color: #f8f9fa; /* Warna latar baris terakhir */
            font-weight: bold;
        }

        .label-cell {
            font-weight: bold;
            color: #555;
            text-align: left;
        }

        .value-cell {
            font-weight: bold;
            text-align: right;
        }

        /* Footer Halaman */
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>Smart Masjid ({{ $settings->nama_masjid ?? 'Al Imaam' }})</p>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
    </div>

    <div class="meta-info">
        <p><span>Filter Laporan:</span> {{ $periodeTeks }}</p>
        
        {{-- LOGIKA TAMPILAN JENIS TRANSAKSI --}}
        <p><span>Jenis Transaksi:</span> 
            @if($tipe == 'pemasukan')
                Pemasukan
            @elseif($tipe == 'pengeluaran')
                Pengeluaran
            @else
                Pemasukan & Pengeluaran
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-date text-center">Tanggal</th>
                <th class="col-type text-center">Tipe</th>
                <th class="col-cat">Kategori</th>
                <th class="col-desc">Deskripsi</th>
                <th class="col-money text-end">Nominal</th>
                <th class="col-money text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            {{-- Baris Saldo Awal: HANYA TAMPIL JIKA FILTER SEMUA & ADA SALDO AWAL --}}
            @if(($tipe == 'semua' || empty($tipe)) && isset($saldoAwal) && $saldoAwal != 0)
            <tr style="background-color: #f0f0f0;">
                <td colspan="5" style="text-align: right; font-weight: bold; font-style: italic;">
                    Saldo Awal (Sebelum Periode Ini)
                </td>
                <td class="text-end" style="font-weight: bold;">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </td>
            </tr>
            @endif

            @forelse ($transaksi as $item)
                @php
                    $isMasuk = $item->tipe == 'pemasukan';
                    // KITA TIDAK BUTUH LOGIKA HITUNG DI SINI LAGI
                    // Cukup ambil data yang sudah dihitung Controller
                    $saldoRow = $item->saldo_berjalan_formatted; 
                @endphp
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <span class="{{ $isMasuk ? 'bg-light-success' : 'bg-light-danger' }}">
                            {{ ucfirst($item->tipe) }}
                        </span>
                    </td>
                    <td>{{ $item->kategori->nama_kategori_keuangan ?? '-' }}</td>
                    <td>{{ $item->deskripsi ?? '-' }}</td>
                    
                    {{-- Nominal --}}
                    <td class="text-end {{ $isMasuk ? 'text-success' : 'text-danger' }}">
                        {{ $isMasuk ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                    
                    {{-- Saldo Berjalan (LANGSUNG DARI CONTROLLER) --}}
                    <td class="text-end" style="font-weight: bold; color: {{ $saldoRow < 0 ? 'red' : '#333' }}">
                        Rp {{ number_format($saldoRow, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada data transaksi untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan Bawah (Summary) --}}
    <div class="summary-container">
        <table class="summary-table">
            {{-- Total Pemasukan --}}
            <tr>
                <td class="label-cell">Total Pemasukan</td>
                <td class="value-cell text-success">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </td>
            </tr>
            
            {{-- Total Pengeluaran --}}
            <tr>
                <td class="label-cell">Total Pengeluaran</td>
                <td class="value-cell text-danger">
                    {{-- Kurung hanya jika nilainya > 0 --}}
                    @if($totalPengeluaran > 0)
                        (Rp {{ number_format($totalPengeluaran, 0, ',', '.') }})
                    @else
                        Rp 0
                    @endif
                </td>
            </tr>

            {{-- Saldo Akhir / Total Akumulasi --}}
            <tr>
                <td class="label-cell">
                    {{-- Ubah Label sesuai konteks --}}
                    @if($tipe == 'pemasukan') Total Pemasukan
                    @elseif($tipe == 'pengeluaran') Total Pengeluaran
                    @else Saldo Akhir
                    @endif
                </td>
                <td class="value-cell" style="color: {{ $saldo < 0 ? 'red' : '#2980b9' }}">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Smart Masjid
    </div>

</body>
</html>