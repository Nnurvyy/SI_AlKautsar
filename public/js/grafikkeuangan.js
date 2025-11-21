// =================================================================
// 1. UTILITY FUNCTION: FORMAT RUPIAH
// =================================================================

/**
 * Fungsi pembantu untuk memformat angka menjadi format Rupiah.
 * @param {number} amount - Nilai numerik.
 * @returns {string} String yang diformat menjadi mata uang IDR.
 */
function formatRupiah(amount) {
    const value = amount || 0; 
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
}

// =================================================================
// 2. LOGIKA UTAMA CHART.JS DAN INTEGRASI API
// =================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Ambil elemen HTML
    const filterRangeSelect = document.getElementById('filterRange');
    const ctx = document.getElementById('GrafikKeuangan');
    
    // **Elemen 'customDateRange', 'startDate', dan 'endDate' Dihapus**
    
    let myChart = null; // Variabel untuk menyimpan instance chart

    // -----------------------------------------------------------
    // A. Fungsi Mengambil Data dari API Laravel
    // -----------------------------------------------------------
    /**
     * Mengambil data keuangan dari endpoint API Laravel.
     * @param {string} range - Nilai filter waktu (misalnya '12_months', '7_days').
     * @returns {object} Data grafik yang sudah diformat dari server.
     */
    async function fetchChartData(range) {
        // URL API hanya menggunakan parameter range
    const url = `/pengurus/grafik/data?range=${range}`;
        
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Gagal mengambil data: HTTP status ${response.status}`);
            }
            const result = await response.json();
            
            // Asumsi Controller Laravel mengembalikan format { data: {...} }
            const dataGrafik = result.data || result;
            
            // **Pastikan data.datasets memiliki properti yang diperlukan Chart.js**
            if (!dataGrafik.datasets || dataGrafik.datasets.length === 0) {
                throw new Error('Data API tidak valid atau kosong.');
            }
            
            return dataGrafik;
            
        } catch (error) {
            console.error('Error saat fetch data grafik:', error);
            // Kembalikan struktur kosong agar chart tidak error
            return {
                labels: [],
                datasets: [
                    { label: 'Pemasukan (Rp)', data: [], backgroundColor: '#28A745' },
                    { label: 'Pengeluaran (Rp)', data: [], backgroundColor: '#DC3545' }
                ]
            };
        }
    }


    // -----------------------------------------------------------
    // B. Konfigurasi Options Chart.js
    // -----------------------------------------------------------
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah (Rupiah)' },
                ticks: {
                    callback: function(value, index, values) {
                        return formatRupiah(value).replace('Rp', '').trim();
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (context.parsed.y !== null) {
                            label = formatRupiah(context.parsed.y);
                        }
                        return label;
                    }
                }
            },
            title: {
                display: true,
                text: 'Laporan Keuangan' 
            }
        }
    };


    // -----------------------------------------------------------
    // C. Fungsi Utama: Mengupdate atau Membuat Chart
    // -----------------------------------------------------------
    async function updateChart(range) {
        // Ambil data dari API
        const data = await fetchChartData(range);
        
        // Update Judul Chart
        const titleText = `Laporan Keuangan: ${range.toUpperCase().replace(/_/g, ' ')}`;
        chartOptions.plugins.title.text = titleText;

        // Inisialisasi atau Update Chart
        if (myChart) {
            // Update Data yang Ada
            myChart.data.labels = data.labels;
            myChart.data.datasets = data.datasets.map(d => ({
                ...d,
                borderWidth: 1,
                borderRadius: 4,
            }));
            myChart.options = chartOptions; 
            myChart.update();
        } else {
            // Buat Chart Baru
            myChart = new Chart(ctx, {
                type: 'bar', 
                data: {
                    labels: data.labels, 
                    datasets: data.datasets.map(d => ({
                        ...d,
                        borderWidth: 1,
                        borderRadius: 4,
                    }))
                },
                options: chartOptions
            });
        }
    }

    // -----------------------------------------------------------
    // D. Event Listener Setup
    // -----------------------------------------------------------
    
    // 1. Inisiasi awal
    updateChart(filterRangeSelect.value); 

    // 2. Event Listener untuk perubahan filter utama
    filterRangeSelect.addEventListener('change', function() {
        const selectedRange = filterRangeSelect.value;
        updateChart(selectedRange);
    });
});