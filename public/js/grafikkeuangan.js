// =================================================================
// 1. MOCK DATA GENERATOR (Simulasi Backend Laravel)
// =================================================================

/**
 * Fungsi untuk menghasilkan data tiruan (mock data) berdasarkan filter waktu.
 * Ini mensimulasikan logika yang seharusnya dilakukan oleh Controller Laravel Anda.
 * * @param {string} range - Nilai dari dropdown filter (misalnya '7_days', '12_months').
 * @returns {object} Objek dengan array labels, pemasukan, dan pengeluaran.
 */
function generateMockData(range) {
    let labels = [];
    let pemasukan = [];
    let pengeluaran = [];
    let numPeriods = 0;
    
    // Tentukan jumlah periode (data points)
    switch (range) {
        case '7_days':
            numPeriods = 7;
            break;
        case '30_days':
            numPeriods = 30;
            break;
        case '12_months':
        case 'current_year':
            numPeriods = 12; 
            break;
        case '5_years':
            numPeriods = 5;
            break;
        case 'custom':
            numPeriods = 10; // Anggap 10 periode data acak untuk kustom
            break;
        default:
            numPeriods = 5;
    }

    const today = new Date();
    
    for (let i = 0; i < numPeriods; i++) {
        let label;
        let randomFactor = Math.random() * 5000000; // Untuk simulasi fluktuasi
        
        // --- LOGIKA LABEL (Meniru format output Laravel) ---
        if (range.includes('days')) {
            // Label Harian
            const date = new Date(today);
            date.setDate(today.getDate() - (numPeriods - 1 - i));
            label = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        } else if (range.includes('month') || range.includes('year') || range === 'current_year') {
            // Label Bulanan/Tahunan
            const date = new Date(today);
            date.setMonth(today.getMonth() - (numPeriods - 1 - i));
            label = date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        } else {
            label = `Periode ${i + 1}`;
        }

        labels.push(label);
        
        // --- DATA NUMERIK (Simulasi Pemasukan dan Pengeluaran) ---
        pemasukan.push(Math.round(10000000 + randomFactor));
        pengeluaran.push(Math.round(8000000 + randomFactor * 0.7));
    }

    return {
        labels: labels,
        pemasukan: pemasukan,
        pengeluaran: pengeluaran
    };
}


// =================================================================
// 2. LOGIKA UTAMA CHART.JS DAN EVENT LISTENER
// =================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Ambil elemen HTML
    const filterRangeSelect = document.getElementById('filterRange');
    const customDateRangeDiv = document.getElementById('customDateRange');
    const ctx = document.getElementById('GrafikKeuangan'); // Pastikan ini ID canvas Anda
    
    let myChart = null; // Variabel untuk menyimpan instance chart

    // -----------------------------------------------------------
    // A. Fungsi Tampilkan/Sembunyikan Range Kustom (Conditional Rendering)
    // -----------------------------------------------------------
    function toggleCustomRangeInput() {
        const selectedValue = filterRangeSelect.value;
        if (selectedValue === 'custom') {
            customDateRangeDiv.style.display = 'flex'; // Tampilkan input tanggal
        } else {
            customDateRangeDiv.style.display = 'none'; // Sembunyikan input tanggal
        }
    }

    // -----------------------------------------------------------
    // B. Fungsi Utama: Mengupdate atau Membuat Chart
    // -----------------------------------------------------------
    function updateChart(range) {
        // --- 1. Ambil Data (Saat ini dari Mock, nanti diganti Fetch API) ---
        // TODO: Ganti ini dengan fetch API ke endpoint Laravel Anda
        // fetch(`/api/keuangan/grafik?range=${range}&start=${startDate}&end=${endDate}`)
        //   .then(response => response.json())
        //   .then(data => {
        //       renderChart(data); // Panggil fungsi renderChart
        //   });
        
        // Menggunakan Mock Data untuk pengujian frontend
        const data = generateMockData(range);

        // --- 2. Render atau Update Chart ---
        if (myChart) {
            // Jika chart sudah ada, perbarui datanya
            myChart.data.labels = data.labels;
            myChart.data.datasets[0].data = data.pemasukan;
            myChart.data.datasets[1].data = data.pengeluaran;
            myChart.update();
        } else {
            // Jika chart belum ada, inisiasi dan buat baru
            myChart = new Chart(ctx, {
                type: 'bar', 
                data: {
                    labels: data.labels, 
                    datasets: [
                        {
                            label: 'Pemasukan (Rp)',
                            data: data.pemasukan,
                            backgroundColor: '#2f42ebcc',
                            borderColor: '#2f42ebcc',
                            borderWidth: 1
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: data.pengeluaran,
                            backgroundColor: '#dc3545',
                            borderColor: '#dc3545',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah (Rupiah)' }
                        }
                    },
                    plugins: {
                        title: { display: true, text: `Laporan Keuangan: ${range.toUpperCase().replace('_', ' ')}` }
                    }
                }
            });
        }
    }

    // -----------------------------------------------------------
    // C. Event Listener Setup
    // -----------------------------------------------------------
    
    // Panggil fungsi saat halaman dimuat (untuk inisiasi tampilan awal)
    toggleCustomRangeInput();
    updateChart(filterRangeSelect.value); 

    // Event Listener untuk perubahan filter utama
    filterRangeSelect.addEventListener('change', function() {
        const selectedRange = filterRangeSelect.value;
        toggleCustomRangeInput();
        
        // HANYA jika bukan custom, update chart
        if (selectedRange !== 'custom') {
            updateChart(selectedRange);
        }
    });

    // Event Listener tambahan untuk input tanggal kustom
    // Tambahkan logika di sini untuk memicu update chart ketika input tanggal kustom berubah
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    startDateInput.addEventListener('change', function() {
        if (startDateInput.value && endDateInput.value) {
            // Jika kedua tanggal sudah diisi, panggil update chart (dengan range 'custom')
            updateChart('custom');
        }
    });

    endDateInput.addEventListener('change', function() {
        if (startDateInput.value && endDateInput.value) {
            updateChart('custom');
        }
    });
});