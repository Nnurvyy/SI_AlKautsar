document.addEventListener('DOMContentLoaded', () => {
    
    let currentPage = 1;
    const tbody = document.querySelector('#tabelLaporan tbody');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationContainer = document.getElementById('paginationLinks');
    const btnFilter = document.getElementById('btnTerapkanFilter');

    
    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    
    const formatTanggal = (dateString) => {
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };

    
    async function loadLaporan() {
        if (!tbody) return;

        
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

        
        const form = document.getElementById('formFilterLaporan');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.append('page', currentPage); 

        try {
            
            const res = await fetch(`/pengurus/lapkeu?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const response = await res.json();

            renderTable(response.data);
            renderPagination(response);

        } catch (err) {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
        }
    }

    
    function renderTable(data) {
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">Tidak ada data transaksi sesuai filter.</td></tr>`;
            return;
        }

        data.forEach(item => {
            
            const isMasuk = item.tipe === 'pemasukan';
            const badgeClass = isMasuk ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
            const nominalClass = isMasuk ? 'text-success' : 'text-danger';
            const symbol = isMasuk ? '+' : '-';
            const kategori = item.kategori ? item.kategori.nama_kategori_keuangan : '-';

            const row = `
                <tr>
                    <td class="text-center">${formatTanggal(item.tanggal)}</td>
                    <td class="text-center">
                        <span class="badge ${badgeClass} rounded-pill text-capitalize">${item.tipe}</span>
                    </td>
                    <td class="text-center">${kategori}</td>
                    <td><div class="d-inline-block text-truncate" style="max-width: 250px;">${item.deskripsi || '-'}</div></td>
                    <td class="text-end fw-bold ${nominalClass}">
                        ${symbol} ${formatRupiah(item.nominal)}
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    
    function renderPagination(response) {
        paginationInfo.textContent = `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
        paginationContainer.innerHTML = '';

        if (response.total === 0) return;

        let ul = document.createElement('ul');
        ul.className = 'pagination justify-content-end mb-0';

        response.links.forEach(link => {
            let li = document.createElement('li');
            
            let label = link.label;
            if (label.includes('Previous')) label = '<';
            else if (label.includes('Next')) label = '>';

            li.className = `page-item ${link.active ? 'active' : ''} ${link.url ? '' : 'disabled'}`;
            
            let btn = document.createElement('button');
            btn.className = 'page-link';
            btn.innerHTML = label;

            if (link.url) {
                btn.onclick = (e) => {
                    e.preventDefault();
                    const urlObj = new URL(link.url);
                    currentPage = urlObj.searchParams.get('page');
                    loadLaporan();
                };
            }
            li.appendChild(btn);
            ul.appendChild(li);
        });
        paginationContainer.appendChild(ul);
    }

    
    if (btnFilter) {
        btnFilter.addEventListener('click', () => {
            currentPage = 1; 
            loadLaporan();
        });
    }

    
    const periodeFilter = document.getElementById('filter-periode');
    const filterBulanan = document.getElementById('filter-bulanan');
    const filterTahunan = document.getElementById('filter-tahunan'); 
    const filterRentang = document.getElementById('filter-rentang'); 

    if (periodeFilter) {
        periodeFilter.addEventListener('change', function() {
            const val = this.value;
            if(filterBulanan) filterBulanan.style.display = 'none';
            if(filterTahunan) filterTahunan.style.display = 'none';
            if(filterRentang) filterRentang.style.display = 'none';

            if (val === 'per_bulan' && filterBulanan) filterBulanan.style.display = 'block';
            if (val === 'per_tahun' && filterTahunan) filterTahunan.style.display = 'block';
            if (val === 'rentang_waktu' && filterRentang) filterRentang.style.display = 'block';
        });
    }

    
    loadLaporan();
});