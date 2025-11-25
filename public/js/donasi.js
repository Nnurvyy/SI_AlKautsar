document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelDonasi tbody');
    
    // Elements Modal (Sama seperti sebelumnya)
    const modalDonasiEl = document.getElementById('modalDonasi');
    const modalDonasi = new bootstrap.Modal(modalDonasiEl);
    const formDonasi = document.getElementById('formDonasi');
    const fotoInput = document.getElementById('foto_donasi');
    const previewFoto = document.getElementById('previewFoto');
    const fotoLabel = document.getElementById('foto_label').querySelector('span');

    const modalDetailEl = document.getElementById('modalDetail');
    const modalDetail = new bootstrap.Modal(modalDetailEl);
    const modalInputPemasukanEl = document.getElementById('modalInputPemasukan');
    const modalInputPemasukan = new bootstrap.Modal(modalInputPemasukanEl);
    const formPemasukan = document.getElementById('formPemasukan');

    // --- 1. STATE MANAGEMENT (BARU) ---
    let state = {
        page: 1,
        search: '',
        status: 'aktif',
        sortBy: 'created_at',
        sortDir: 'desc'
    };
    let currentDonasiId = null;

    // Helper Format
    const formatRupiah = (angka) => "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    const formatTanggal = (str) => {
        if (!str) return '-';
        return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    // =========================================
    // LOAD DATA DENGAN FILTER & SORT
    // =========================================
    async function loadDonasi() {
        // Hitung colspan berdasarkan jumlah kolom header
        const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        try {
            // Buat Query String dari State
            const params = new URLSearchParams({
                page: state.page,
                search: state.search,
                status: state.status,
                sortBy: state.sortBy,
                sortDir: state.sortDir
            });

            const res = await fetch(`/pengurus/donasi-data?${params.toString()}`);
            const response = await res.json();
            renderTable(response.data, response.from || 1);
            renderPagination(response);
            updateSortIcons(); // Update ikon panah
        } catch (err) {
            const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = '';
        const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
        
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada program donasi.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const target = parseFloat(item.target_dana);
            const terkumpul = parseFloat(item.total_terkumpul);
            const persen = target > 0 ? Math.min(100, (terkumpul / target) * 100).toFixed(1) : 0;
            const fotoUrl = item.foto_donasi ? `/storage/${item.foto_donasi}` : 'https://via.placeholder.com/60?text=No+Img';

            // Cek Status Lewat untuk styling (Opsional)
            const isExpired = item.tanggal_selesai && new Date(item.tanggal_selesai) < new Date().setHours(0,0,0,0);
            const rowClass = isExpired ? 'table-secondary text-muted' : '';

            const row = `
                <tr class="${rowClass}">
                    <td class="text-center">${startNum + i}</td>
                    <td class="text-center">
                        <img src="${fotoUrl}" class="rounded" style="width:50px; height:50px; object-fit:cover;">
                    </td>
                    <td>
                        <div class="fw-bold">${item.nama_donasi}</div>
                        ${isExpired ? '<span class="badge bg-secondary" style="font-size:0.6rem">Berakhir</span>' : ''}
                    </td>
                    
                    <td class="text-center small">${formatTanggal(item.tanggal_mulai)}</td>
                    <td class="text-center small">${item.tanggal_selesai ? formatTanggal(item.tanggal_selesai) : '<span class="badge bg-success">Unlimited</span>'}</td>

                    <td class="text-end">${formatRupiah(target)}</td>
                    <td class="text-end text-success fw-bold">${formatRupiah(terkumpul)}</td>
                    <td class="text-center">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: ${persen}%"></div>
                        </div>
                        <small>${persen}%</small>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-info btn-sm text-white" onclick="window.bukaDetail('${item.id_donasi}')" title="Lihat Pemasukan"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-warning btn-sm" onclick="window.editDonasi('${item.id_donasi}')"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="window.hapusDonasi('${item.id_donasi}')"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    // --- 2. EVENT LISTENERS FILTER & SEARCH ---
    
    // Search
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            state.search = e.target.value;
            state.page = 1;
            loadDonasi();
        }, 300);
    });

    // Filter Status
    document.getElementById('statusFilter').addEventListener('change', (e) => {
        state.status = e.target.value;
        state.page = 1;
        loadDonasi();
    });

    // Sorting
    function handleSort(column) {
        if (state.sortBy === column) {
            state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            state.sortBy = column;
            state.sortDir = 'asc'; // Default asc kalau ganti kolom
        }
        loadDonasi();
    }

    // Bind click event untuk header sorting
    document.getElementById('sortMulai').addEventListener('click', () => handleSort('tanggal_mulai'));
    document.getElementById('sortSelesai').addEventListener('click', () => handleSort('tanggal_selesai'));

    function updateSortIcons() {
        // Reset semua icon
        document.querySelectorAll('.sort-icon').forEach(i => i.className = 'bi bi-arrow-down-up small text-muted sort-icon');
        
        // Set icon aktif
        let activeIconClass = state.sortDir === 'asc' ? 'bi bi-arrow-up text-primary' : 'bi bi-arrow-down text-primary';
        
        if (state.sortBy === 'tanggal_mulai') {
            document.querySelector('#sortMulai i').className = `${activeIconClass} small sort-icon`;
        } else if (state.sortBy === 'tanggal_selesai') {
            document.querySelector('#sortSelesai i').className = `${activeIconClass} small sort-icon`;
        }
    }

    // Pagination logic (Sederhana)
    function renderPagination(response) {
        const info = document.getElementById('paginationInfo');
        const nav = document.getElementById('paginationLinks');
        
        info.textContent = `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
        
        let linksHtml = '<ul class="pagination justify-content-end mb-0">';
        response.links.forEach(link => {
            let active = link.active ? 'active' : '';
            let disabled = link.url ? '' : 'disabled';
            let label = link.label.replace('&laquo; Previous', '<').replace('Next &raquo;', '>');
            linksHtml += `<li class="page-item ${active} ${disabled}"><a class="page-link" href="#" data-url="${link.url}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        nav.innerHTML = linksHtml;

        // Bind click pagination
        nav.querySelectorAll('a.page-link').forEach(a => {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                if (a.dataset.url && a.dataset.url !== 'null') {
                    const urlParams = new URLSearchParams(a.dataset.url.split('?')[1]);
                    state.page = urlParams.get('page');
                    loadDonasi();
                }
            });
        });
    }

    // --- CRUD Events (Sama seperti sebelumnya) ---
    document.getElementById('btnTambahDonasi').addEventListener('click', () => {
        formDonasi.reset();
        document.getElementById('id_donasi').value = '';
        previewFoto.classList.add('d-none');
        fotoLabel.textContent = "Pilih gambar...";
        document.getElementById('modalTitle').textContent = "Program Donasi Baru";
        modalDonasi.show();
    });

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fotoLabel.textContent = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                previewFoto.src = e.target.result;
                previewFoto.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    formDonasi.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('id_donasi').value;
        const formData = new FormData(formDonasi);
        let url = '/pengurus/donasi';
        if (id) { url += `/${id}`; formData.append('_method', 'PUT'); }

        try {
            const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: formData });
            const data = await res.json();
            if (res.ok) {
                modalDonasi.hide();
                Swal.fire('Sukses', data.message, 'success');
                loadDonasi();
            } else {
                throw new Error(data.message || 'Error validasi');
            }
        } catch (err) { Swal.fire('Error', err.message, 'error'); }
    });

    window.editDonasi = async (id) => {
        const res = await fetch(`/pengurus/donasi/${id}`);
        const data = await res.json();
        document.getElementById('id_donasi').value = data.id_donasi;
        document.getElementById('nama_donasi').value = data.nama_donasi;
        document.getElementById('target_dana').value = data.target_dana;
        document.getElementById('tanggal_mulai').value = data.tanggal_mulai.split('T')[0];
        if(data.tanggal_selesai) document.getElementById('tanggal_selesai').value = data.tanggal_selesai.split('T')[0];
        document.getElementById('deskripsi').value = data.deskripsi || '';
        if (data.foto_donasi) {
            previewFoto.src = data.foto_url;
            previewFoto.classList.remove('d-none');
            fotoLabel.textContent = "Ganti gambar...";
        } else {
            previewFoto.classList.add('d-none');
            fotoLabel.textContent = "Pilih gambar...";
        }
        document.getElementById('modalTitle').textContent = "Edit Program Donasi";
        modalDonasi.show();
    };

    window.hapusDonasi = async (id) => {
        const c = await Swal.fire({ title: 'Hapus?', text: 'Data akan hilang!', icon: 'warning', showCancelButton: true });
        if (c.isConfirmed) {
            await fetch(`/pengurus/donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            loadDonasi();
            Swal.fire('Terhapus', '', 'success');
        }
    };

    // --- DETAIL & PEMASUKAN LOGIC (Sama seperti sebelumnya, dipersingkat) ---
    window.bukaDetail = async (id) => { currentDonasiId = id; await refreshModalDetail(); modalDetail.show(); };
    async function refreshModalDetail() {
        if(!currentDonasiId) return;
        const res = await fetch(`/pengurus/donasi/${currentDonasiId}`);
        const data = await res.json();
        document.getElementById('detailTitle').textContent = data.nama_donasi;
        document.getElementById('input_id_donasi').value = data.id_donasi;
        const target = parseFloat(data.target_dana);
        const terkumpul = parseFloat(data.total_terkumpul);
        document.getElementById('detTarget').textContent = formatRupiah(target);
        document.getElementById('detTerkumpul').textContent = formatRupiah(terkumpul);
        document.getElementById('detSisa').textContent = formatRupiah(Math.max(0, target - terkumpul));
        
        const tbodyRiwayat = document.getElementById('tabelRiwayat');
        tbodyRiwayat.innerHTML = '';
        if (data.pemasukan.length === 0) { tbodyRiwayat.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada pemasukan.</td></tr>`; }
        else {
            data.pemasukan.forEach(p => {
                // ... (Logic render row history sama) ...
                let badge = p.metode_pembayaran === 'tunai' ? '<span class="badge bg-secondary">Tunai</span>' : '<span class="badge bg-primary">Transfer</span>';
                tbodyRiwayat.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${formatTanggal(p.tanggal)}</td>
                        <td>${p.nama_donatur} <br> <small class="text-muted">${p.pesan || ''}</small></td>
                        <td class="text-center">${badge}</td>
                        <td class="text-end">${formatRupiah(p.nominal)}</td>
                        <td class="text-center"><button class="btn btn-sm btn-outline-danger" onclick="window.hapusPemasukan('${p.id_pemasukan_donasi}')">&times;</button></td>
                    </tr>
                `);
            });
        }
    }

    formPemasukan.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formPemasukan);
        try {
            const res = await fetch('/pengurus/pemasukan-donasi', { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: formData });
            if (res.ok) {
                modalInputPemasukan.hide(); formPemasukan.reset();
                Swal.fire({ icon: 'success', title: 'Masuk!', timer: 1000, showConfirmButton: false });
                await refreshModalDetail(); loadDonasi();
            }
        } catch (err) { Swal.fire('Gagal', '', 'error'); }
    });

    window.hapusPemasukan = async (id) => {
        const c = await Swal.fire({ title: 'Hapus?', icon: 'warning', showCancelButton: true });
        if(c.isConfirmed) {
            await fetch(`/pengurus/pemasukan-donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            await refreshModalDetail(); loadDonasi();
        }
    };

    // Init Load
    loadDonasi();
});