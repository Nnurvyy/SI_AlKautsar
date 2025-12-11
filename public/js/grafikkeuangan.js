document.addEventListener('DOMContentLoaded', function() {
    
    const filterRangeSelect = document.getElementById('filterRange');
    const ctx = document.getElementById('GrafikKeuangan');
    let myChart = null;

    
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(amount || 0);
    }

    
    async function updateDashboardData(range) {
        
        document.getElementById('containerAlokasiPemasukan').innerHTML = '<small class="text-muted">Loading...</small>';
        document.getElementById('containerAlokasiPengeluaran').innerHTML = '<small class="text-muted">Loading...</small>';

        try {
            const response = await fetch(`/pengurus/grafik/data?range=${range}`);
            const result = await response.json();

            if (result.status === 'success') {
                
                renderChart(result.chart, range);
                
                
                renderAllocationBars(result.allocation);
                
                
                const labelText = filterRangeSelect.options[filterRangeSelect.selectedIndex].text;
                document.getElementById('labelAlokasi').textContent = labelText;
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    
    function renderChart(chartData, range) {
        
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (val) => formatRupiah(val).replace('Rp', '').trim() }
                }
            }
        };

        if (myChart) {
            myChart.data.labels = chartData.labels;
            myChart.data.datasets = chartData.datasets;
            myChart.update();
        } else {
            myChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: options
            });
        }
    }

    
    function renderAllocationBars(allocationData) {
        const containerIn = document.getElementById('containerAlokasiPemasukan');
        const containerOut = document.getElementById('containerAlokasiPengeluaran');

        containerIn.innerHTML = generateBarHTML(allocationData.pemasukan, 'success');
        containerOut.innerHTML = generateBarHTML(allocationData.pengeluaran, 'danger');
    }

    function generateBarHTML(items, colorClass) {
        if (!items || items.length === 0) {
            return '<small class="text-muted fst-italic">Tidak ada data untuk periode ini.</small>';
        }

        let html = '';
        items.forEach(item => {
            html += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-dark">${item.kategori}</span>
                        <span class="small text-${colorClass} fw-bold">${item.persentase}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-${colorClass}" role="progressbar" 
                             style="width: ${item.persentase}%" 
                             aria-valuenow="${item.persentase}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="text-end mt-1">
                        <small class="text-muted" style="font-size: 0.75rem;">${formatRupiah(item.total)}</small>
                    </div>
                </div>
            `;
        });
        return html;
    }

    
    
    updateDashboardData(filterRangeSelect.value);

    
    filterRangeSelect.addEventListener('change', function() {
        updateDashboardData(this.value);
    });
});