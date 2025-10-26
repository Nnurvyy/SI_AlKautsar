function initLaporanExport(transaksiData, totalPemasukan, totalPengeluaran, totalSaldo) {

    // --- Helper Functions ---

    // Format helper untuk Rupiah
    const formatRupiah = (angka) => {
        if (angka === null || isNaN(angka)) return 'Rp 0';
        const isNegatif = angka < 0;
        const number_string = Math.abs(angka).toString().replace(/[^,\d]/g, '');
        const split = number_string.split(',');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return (isNegatif ? '-Rp ' : 'Rp ') + rupiah;
    };

    // Format helper untuk Tanggal (dd/mm/yyyy)
    const formatTanggal = (tanggal) => {
        if (!tanggal) return 'N/A';
        try {
            return new Date(tanggal).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return tanggal; // fallback jika format tidak valid
        }
    };

    // --- Logika Tombol Export Excel ---
    const btnExcel = document.getElementById('export-excel');
    if (btnExcel) {
        btnExcel.addEventListener('click', function () {
            // Cek jika library XLSX sudah dimuat
            if (typeof XLSX === 'undefined') {
                alert('Library export Excel (XLSX) belum dimuat.');
                return;
            }

            // Judul
            const headers = ['Tanggal', 'Tipe', 'Kategori', 'Divisi', 'Deskripsi', 'Jumlah'];

            // Ubah data transaksi (JSON) menjadi array
            const dataBody = transaksiData.map(item => [
                formatTanggal(item.tanggal_transaksi),
                item.tipe,
                item.kategori ? item.kategori.nama_kategori : 'N/A', // Cek jika relasi ada
                item.divisi ? item.divisi.nama_divisi : 'N/A',     // Cek jika relasi ada
                item.deskripsi,
                item.jumlah // Gunakan 'jumlah' (angka) agar Excel bisa SUM
            ]);

            // Siapkan data Ringkasan
            const dataRingkasan = [
                [], // Baris kosong
                ['RINGKASAN', '', '', '', '', ''],
                ['Total Pemasukan', '', '', '', '', totalPemasukan],
                ['Total Pengeluaran', '', '', '', '', totalPengeluaran],
                ['Saldo', '', '', '', '', totalSaldo]
            ];

            // Gabungkan header, data, dan ringkasan
            const finalData = [headers, ...dataBody, ...dataRingkasan];

            // Buat Workbook dan Worksheet
            const ws = XLSX.utils.aoa_to_sheet(finalData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Laporan Keuangan');

            // Generate file Excel
            XLSX.writeFile(wb, `Laporan-Keuangan-${Date.now()}.xlsx`);
        });
    }


    // --- Logika Tombol Export PDF ---
    const btnPdf = document.getElementById('export-pdf');
    if (btnPdf) {
        btnPdf.addEventListener('click', function () {

            // Cek jika library jsPDF dan autoTable sudah dimuat
            if (typeof jspdf === 'undefined' || typeof jspdf.plugin.autotable === 'undefined') {
                alert('Library export PDF (jsPDF) atau plugin autoTable belum dimuat.');
                return;
            }

            // Inisialisasi jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const tglCetak = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

            // Judul Dokumen
            doc.setFontSize(18);
            doc.text('Laporan Keuangan', 14, 22);

            // Info Tambahan
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text(`Tanggal Cetak: ${tglCetak}`, 14, 28);
            doc.text('Periode: Semua Periode', 14, 32);

            // Data Ringkasan
            doc.setFontSize(11);
            doc.setTextColor(0);
            doc.text('Ringkasan:', 14, 42);
            doc.setFont(undefined, 'bold');
            doc.text('Total Pemasukan:', 14, 48);
            doc.text(formatRupiah(totalPemasukan), 55, 48);

            doc.text('Total Pengeluaran:', 14, 54);
            doc.text(formatRupiah(totalPengeluaran), 55, 54);

            doc.text('Saldo:', 14, 60);
            doc.text(formatRupiah(totalSaldo), 55, 60);

            // Header Tabel
            const tableHeaders = ['Tanggal', 'Tipe', 'Kategori', 'Divisi', 'Deskripsi', 'Jumlah'];

            // Data Body Tabel
            const tableBody = transaksiData.map(item => [
                formatTanggal(item.tanggal_transaksi),
                item.tipe,
                item.kategori ? item.kategori.nama_kategori : 'N/A',
                item.divisi ? item.divisi.nama_divisi : 'N/A',
                item.deskripsi,
                formatRupiah(item.jumlah) // Format sebagai string Rupiah
            ]);

            // Buat tabel menggunakan AutoTable
            doc.autoTable({
                head: [tableHeaders],
                body: tableBody,
                startY: 70, // Mulai tabel setelah ringkasan
                theme: 'grid',
                headStyles: { fillColor: [248, 250, 252], textColor: 100 },
                styles: { fontSize: 8 },
                didDrawCell: (data) => {
                    // Atur styling khusus untuk kolom 'Jumlah'
                    if (data.column.dataKey === 5) { // Kolom 'Jumlah' (indeks 5)
                        data.cell.styles.halign = 'right'; // Rata kanan
                        if (data.row.section === 'body') {
                            // Cek data asli (angka) sebelum diformat
                            const jumlahAsli = transaksiData[data.row.index].jumlah;
                            if (jumlahAsli < 0) {
                                data.cell.styles.textColor = [220, 38, 38]; // Warna merah
                            } else {
                                data.cell.styles.textColor = [22, 163, 74]; // Warna hijau
                            }
                        }
                    }
                }
            });

            // Simpan file PDF
            doc.save(`Laporan-Keuangan-${Date.now()}.pdf`);
        });
    }
}
