<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Memuat font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Menggunakan font Inter sebagai default */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <!-- Set locale Carbon untuk format tanggal bahasa Indonesia -->
    <?php \Carbon\Carbon::setLocale('id'); ?>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Anda bisa mengganti ini dengan layout Anda -->
<!-- START: Konten Halaman -->
<div class="container mx-auto p-4 md:p-8">

    <!-- Bagian Header Halaman -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            Laporan Keuangan
        </h1>
        <p class="text-gray-500">
            <!-- Mengambil tanggal hari ini, contoh: Minggu, 26 Oktober 2025 -->
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    <!-- Card: Filter Laporan -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <h2 class="text-lg font-semibold mb-5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-1.097 2.028l-2.093 1.047a2.25 2.25 0 00-1.097 2.028v1.044c0 .54.384 1.006.917 1.096C20.32 15.66 23 15.89 23 18v1.5c0 .552-.448 1-1 1h-1.5c-.552 0-1-.448-1-1V18c0-2.11.232-4.81.678-7.438.09-.533-.294-1.006-.827-1.096l-2.093-1.047a2.25 2.25 0 01-1.097-2.028V5.37c0-.54-.384-1.006-.917-1.096A48.31 48.31 0 0012 3zM3.917 4.772c.533-.09.917-.556.917-1.096V2.632c0-.552.448-1 1-1h1.5c.552 0 1 .448 1 1v1.044c0 .54-.384 1.006-.917 1.096C4.68 4.908 2 5.138 2 7.25v1.5c0 .552.448 1 1 1h1.5c.552 0 1-.448 1-1v-1.5c0-2.11-.232-4.81-.678-7.438-.09-.533.294-1.006.827-1.096l2.093-1.047a2.25 2.25 0 011.097-2.028V1.37c0-.54.384-1.006.917-1.096A48.31 48.31 0 0012 0c-2.755 0-5.455.232-8.083.678-.533.09-.917.556-.917 1.096v1.044a2.25 2.25 0 01-1.097 2.028L.728 5.89a2.25 2.25 0 00-1.097 2.028v1.044c0 .54.384 1.006.917 1.096C3.68 10.19 6 10.42 6 12.5v1.5c0 .552.448 1 1 1h1.5c.552 0 1-.448 1-1v-1.5c0-2.11.232-4.81.678-7.438.09-.533-.294-1.006-.827-1.096L7.228 3.91a2.25 2.25 0 01-1.097-2.028V.83c0-.54-.384-1.006-.917-1.096A48.31 48.31 0 000 0c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 011.097 2.028l2.093 1.047a2.25 2.25 0 001.097 2.028v1.044c0 .54-.384 1.006-.917 1.096C9.32 9.09 7 9.32 7 11.43v1.5c0 .552-.448 1-1 1H4.5c-.552 0-1-.448-1-1v-1.5c0-2.11-.232-4.81-.678-7.438-.09-.533.294-1.006.827-1.096l2.093-1.047a2.25 2.25 0 011.097-2.028V1.83c0-.54.384-1.006.917-1.096C10.32.32 12 .11 12 0h-1.5c-.552 0-1 .448-1 1v1.044c0 .54.384 1.006.917 1.096.22.037.437.078.65.122.214.044.425.09.632.138.207.048.41.1.608.154.198.055.39.112.578.17.188.058.37.118.547.18.177.062.348.127.513.194.165.067.323.137.476.21.153.072.298.147.438.224.14.077.273.156.4.238.126.082.245.166.358.25.113.085.218.172.317.26.1.088.19.178.275.27.085.092.16.185.23.28s.13.19.186.287c.056.097.103.195.14.295.038.1.066.2.085.3.018.1.028.2.03.3v.3c0 .1-.01.2-.03.3a.996.996 0 00-.085.3c-.037.1-.084.198-1.14.295-.056.097-.12.19-.186.287s-.145.198-.23.28c-.085.092-.175.182-.275.27-.1.088-.204.175-.317.26-.113.084-.232.168-.358.25-.127.082-.26.16-.4.238-.14.077-.285.152-.438.224-.165.067-.324.137-.476.21-.165.067-.336.132-.513.194-.177.062-.36.122-.547.18-.188.058-.38.115-.578.17-.198.055-.401.106-.608.154-.207.048-.418.094-.632.138-.213.044-.43.085-.65.122-.533.09-.917.556-.917 1.096v1.044a2.25 2.25 0 01-1.097 2.028l-2.093 1.047a2.25 2.25 0 00-1.097 2.028v1.044c0 .54.384 1.006.917 1.096C7.68 15.66 10 15.89 10 18v1.5c0 .552.448 1 1 1h1.5c.552 0 1-.448 1-1V18c0-2.11.232-4.81.678-7.438.09-.533-.294-1.006-.827-1.096l-2.093-1.047a2.25 2.25 0 01-1.097-2.028V6.38c0-.54-.384-1.006-.917-1.096a48.31 48.31 0 00-3.232-.612z" />
            </svg>
            Filter Laporan
        </h2>

        <!-- Form untuk filter -->
        <!-- Anda bisa tambahkan action & method nanti -->
        <form action="" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
                <!-- Filter Tipe Transaksi -->
                <div>
                    <label for="tipe_transaksi" class="block text-sm font-medium text-gray-700">Tipe Transaksi</label>
                    <select id="tipe_transaksi" name="tipe_transaksi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="semua">Semua</option>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>

                <!-- Filter Periode Waktu -->
                <div>
                    <label for="periode_waktu" class="block text-sm font-medium text-gray-700">Periode Waktu</label>
                    <select id="periode_waktu" name="periode_waktu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="semua">Semua</option>
                        <option value="harian">Hari Ini</option>
                        <option value="mingguan">Minggu Ini</option>
                        <option value="bulanan">Bulan Ini</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex space-x-3">
                <!-- Tombol Export PDF -->
                <a href="#" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1.5">
                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6.414A2.25 2.25 0 0015.414 4L12 1.586A2.25 2.25 0 0010.414 1H4.25A2.25 2.25 0 002 3.25V4a2 2 0 002 2zm2 4.5a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 016 6.5zm0 3a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 016 9.5zm0 3a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 016 12.5z" clip-rule="evenodd" />
                    </svg>
                    Export PDF
                </a>
                <!-- Tombol Export Excel -->
                <a href="#" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1.5">
                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6.414A2.25 2.25 0 0015.414 4L12 1.586A2.25 2.25 0 0010.414 1H4.25A2.25 2.25 0 002 3.25V4a2 2 0 002 2zm2 4.5a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 016 6.5zm0 3a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 016 9.5zm0 3a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 016 12.5z" clip-rule="evenodd" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Grid: 3 Kartu Total (Pemasukan, Pengeluaran, Saldo) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Card Total Pemasukan -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Pemasukan</p>
            <p class="mt-1 text-3xl font-semibold text-green-600">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </p>
        </div>

        <!-- Card Total Pengeluaran -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Pengeluaran</p>
            <p class="mt-1 text-3xl font-semibold text-red-600">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </p>
        </div>

        <!-- Card Saldo -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-sm font-medium text-gray-500">Saldo</p>
            <!-- Logika untuk warna Saldo (Merah jika minus) -->
            @if ($totalSaldo < 0)
                <p class="mt-1 text-3xl font-semibold text-red-600">
                    -Rp {{ number_format(abs($totalSaldo), 0, ',', '.') }}
                </p>
            @else
                <p class="mt-1 text-3xl font-semibold text-green-600">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </p>
            @endif
        </div>
    </div>

    <!-- Card: Tabel Data Transaksi -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header Card Tabel -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold">
                Data Transaksi - Semua Periode
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                <!-- Menggunakan variabel $transaksi dari controller -->
                {{ $transaksi->count() }} transaksi
            </p>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipe
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kategori
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Divisi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Deskripsi
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jumlah
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                <!-- Looping data dari controller -->
                @forelse ($transaksi as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <!-- Format tanggal transaksi -->
                            {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <!-- Logika badge Merah/Hijau berdasarkan 'tipe' -->
                            @if ($item->tipe == 'Pengeluaran')
                                <span class="inline-flex items-center rounded-md bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                        {{ $item->tipe }}
                                    </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        {{ $item->tipe }}
                                    </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <!-- Data dari relasi kategori -->
                            {{ $item->kategori->nama_kategori ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <!-- Data dari relasi divisi -->
                            {{ $item->divisi->nama_divisi ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $item->deskripsi }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            <!-- Logika warna Merah/Hijau berdasarkan 'jumlah' -->
                            @if ($item->jumlah < 0)
                                <span class="text-red-600">
                                        -Rp {{ number_format(abs($item->jumlah), 0, ',', '.') }}
                                    </span>
                            @else
                                <span class="text-green-600">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <!-- Tampilan jika data $transaksi kosong -->
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data transaksi untuk ditampilkan.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- 1. Memuat Library JS (SheetJS, jsPDF, jsPDF-AutoTable) -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<!--
  2. Memuat Skrip Kustom Anda
  (Gunakan 'asset()' untuk path yang benar ke folder 'public/js/')
-->
<script src="{{ asset('js/create_laporan.js') }}"></script>

<!--
  3. Skrip "Jembatan" (Inline)
  Tugas skrip ini HANYA mengirim data PHP ke JS.
-->
<script>
    // Pastikan halaman sudah dimuat
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Ambil data dari PHP (Controller) menggunakan @json
        // .values() penting untuk mengubah koleksi Laravel jadi array murni
        data = @json($transaksi=>values());
        totalPemasukan = @json($totalPemasukan);
        totalPengeluaran = @json($totalPengeluaran);
        totalSaldo = @json($totalSaldo);

        // 2. Panggil fungsi dari file eksternal (laporan-export.js)
        //    dan kirim datanya sebagai parameter
        initLaporanExport(data, totalPemasukan, totalPengeluaran, totalSaldo);
    });
</script>

</body>
</html>
