document.addEventListener('DOMContentLoaded', () => {

    // --- 1. Definisi Elemen Utama ---
    
    // Asumsi: ID elemen-elemen ini ada di artikel.blade.php
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tbody = document.querySelector('#tabelArtikel tbody'); 
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // --- State Management ---
    let state = {
        currentPage: 1,
        // Status awal 'all' untuk memuat semua data saat pertama kali dibuka
        status: 'all', 
        search: '',
        perPage: 10,
        sortBy: 'tanggal_terbit_artikel', 
        sortDir: 'desc',        
        searchTimeout: null
    };

    // ------------------------------------------------------------------
    // 2. FUNGSI UTAMA: LOAD DATA
    // ------------------------------------------------------------------

    async function loadArtikel() { 
        if (!tbody) return;
        
        // 2.1. Tampilkan Loading
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        // 2.2. Bangun URL API
        const url = `/admin/artikel-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal memuat data artikel');
            
            const response = await res.json(); 
            
            renderTable(response.data, response.from || 1);
            renderPagination(response); 
            
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">${err.message}</td></tr>`;
            paginationInfo.textContent = 'Gagal memuat data';
            paginationContainer.innerHTML = '';
        }
    }

    // ------------------------------------------------------------------
    // 3. FUNGSI RENDER (FORMATTING & TABLE)
    // ------------------------------------------------------------------

    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';
        // Asumsi tanggal terbit adalah YYYY-MM-DD
        const date = new Date(tanggalStr); 
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            : 'Invalid Date';
    }
    
    // Fungsi untuk menampilkan badge status
    const statusBadge = (status) => {
        let color;
        switch (status) {
            case 'published':
                color = 'badge bg-success'; 
                break;
            case 'draft':
                color = 'badge bg-secondary'; 
                break;
            default:
                color = 'badge bg-info';
        }
        const statusText = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Belum Ditentukan'; 
        return `<span class="${color}">${statusText}</span>`;
    }; 
    

    function renderTable(data, startingNumber) {
        const tbody = document.querySelector('#tabelArtikel tbody'); 
        tbody.innerHTML = ''; 
        
        if (data.length === 0) {
            let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data artikel.</td></tr>`;
            return;
        }
        
        // Fungsi formatTanggal dan statusBadge harus didefinisikan/dapat diakses
        const formatTanggal = (tanggalStr) => { 
            // ... (fungsi formatTanggal dari kode sebelumnya) ...
            if (!tanggalStr) return '-';
            const date = new Date(tanggalStr); 
            return !isNaN(date)
                ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                : 'Invalid Date';
        };

        const statusBadge = (status) => {
            // ... (fungsi statusBadge dari kode sebelumnya) ...
            let color;
            switch (status) {
                case 'published': color = 'badge bg-success'; break;
                case 'draft': color = 'badge bg-secondary'; break;
                default: color = 'badge bg-info';
            }
            const statusText = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Belum Ditentukan'; 
            return `<span class="${color}">${statusText}</span>`;
        }; 
        

        data.forEach((item, i) => {
            
            const row = `
            <tr>
                <td class="text-center">${startingNumber + i}</td>
                
                <td>
                    <img src="${item.foto_url || '/images/default_artikel.png'}" 
                        alt="Foto" 
                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                
                <td>${item.judul_artikel}</td>
                
                <td>${item.penulis_artikel}</td>
                
                <td class="text-center">${formatTanggal(item.tanggal_terbit_artikel)}</td>
                
                <td class="text-center">${statusBadge(item.status_artikel)}</td>
                
                <td class="text-center">
                    
                    <button class="btn btn-info btn-sm text-white me-1" onclick="showDetailArtikel('${item.id_artikel}')">
                        <i class="bi bi-eye"></i>
                    </button>
                    
                    <a href="/admin/artikel/${item.id_artikel}/edit" class="btn btn-warning btn-sm me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    
                    <button class="btn btn-danger btn-sm" onclick="hapusArtikel('${item.id_artikel}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }
// PENTING: Jangan lupa definisikan fungsi showDetailArtikel(id) dan hapusArtikel(id) di scope global/artikel_index.js

    function renderPagination(response) {
        const { from, to, total, links } = response;

        if (total === 0) {
            paginationInfo.textContent = 'Menampilkan 0 dari 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        paginationInfo.textContent = `Menampilkan ${from} - ${to} dari ${total} data`;

        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        
        links.forEach(link => {
            let label = link.label;
            if (label.includes('Previous')) label = '&laquo;';
            else if (label.includes('Next')) label = '&raquo;';
            
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';

            linksHtml += `
                <li class="page-item ${disabled} ${active}">
                    <a class="page-link" href="${link.url || '#'}">${label}</a>
                </li>
            `;
        });

        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // ------------------------------------------------------------------
    // 4. EVENT LISTENERS
    // ------------------------------------------------------------------

    // 4.1. Search Bar
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; 
                loadArtikel(); 
            }, 300); 
        });
    }

    // 4.2. Filter Status
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            state.status = statusFilter.value;
            state.currentPage = 1; 
            loadArtikel(); 
        });
    }

    // 4.3. Klik Pagination
    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link'); 
            
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) {
                return; 
            }

            const url = new URL(target.href);
            const page = url.searchParams.get('page'); 
            
            if (page) {
                state.currentPage = parseInt(page);
                loadArtikel(); 
            }
        });
    }
    
    // ------------------------------------------------------------------
    // 5. FUNGSI GLOBAL: HAPUS ARTIKEL (AJAX)
    // ------------------------------------------------------------------
    
    window.hapusArtikel = async function(id_artikel) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data artikel akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`/admin/artikel/${id_artikel}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadArtikel(); // Muat ulang data
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- 6. INISIALISASI ---
    loadArtikel(); 
});