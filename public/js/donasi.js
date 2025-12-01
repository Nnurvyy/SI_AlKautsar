document.addEventListener('DOMContentLoaded', () => {
    // 1. SETUP & VARIABEL GLOBAL
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelDonasi tbody');
    
    // Modal Utama
    const modalDonasiEl = document.getElementById('modalDonasi');
    const modalDonasi = new bootstrap.Modal(modalDonasiEl);
    const formDonasi = document.getElementById('formDonasi');
    
    // Element Foto & UI Baru
    const fotoInput = document.getElementById('foto_donasi');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder'); // Kotak Dashed
    const previewContainer = document.getElementById('previewContainer');   // Wrapper Gambar
    const previewFoto = document.getElementById('previewFoto');             // Gambar <img>
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    
    // Element Rupiah
    const displayTarget = document.getElementById('display_target_dana');
    const realTarget = document.getElementById('target_dana');

    // Modal Detail
    const modalDetailEl = document.getElementById('modalDetail');
    const modalDetail = new bootstrap.Modal(modalDetailEl);
    const modalInputPemasukanEl = document.getElementById('modalInputPemasukan');
    const modalInputPemasukan = new bootstrap.Modal(modalInputPemasukanEl);
    const formPemasukan = document.getElementById('formPemasukan');

    // State Management
    let state = {
        page: 1, search: '', status: 'aktif', sortBy: 'created_at', sortDir: 'desc'
    };
    let currentDonasiId = null;

    // Helper
    const formatRupiah = (angka) => "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    const formatTanggal = (str) => {
        if (!str) return '-';
        return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    // ============================================================
    // 2. LOGIC PREVIEW FOTO (MODEL SWAP CONTAINER)
    // ============================================================

    // Fungsi Helper: Tampilkan Gambar, Sembunyikan Kotak Upload
    function showPreview(src) {
        previewFoto.src = src;
        uploadPlaceholder.classList.add('d-none'); // Sembunyikan kotak dashed
        previewContainer.classList.remove('d-none'); // Tampilkan gambar besar
    }

    // Fungsi Helper: Reset ke Mode Upload
    function resetPreview() {
        fotoInput.value = '';
        previewFoto.src = '';
        uploadPlaceholder.classList.remove('d-none'); // Munculkan kotak dashed
        previewContainer.classList.add('d-none'); // Sembunyikan gambar besar
    }

    // Event saat pilih file
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                showPreview(e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Event Hapus Foto
    btnHapusFoto.addEventListener('click', () => {
        resetPreview();
    });

    // ============================================================
    // 3. LOGIC FORMAT RUPIAH
    // ============================================================
    displayTarget.addEventListener('keyup', function(e) {
        let rawValue = this.value.replace(/[^0-9]/g, '');
        realTarget.value = rawValue;
        if (rawValue) {
            this.value = new Intl.NumberFormat('id-ID').format(rawValue);
        } else {
            this.value = '';
        }
    });

    // ============================================================
    // 4. LOAD DATA (READ)
    // ============================================================
    async function loadDonasi() {
        const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success"></div></td></tr>`;

        try {
            const params = new URLSearchParams({
                page: state.page, search: state.search, status: state.status, sortBy: state.sortBy, sortDir: state.sortDir
            });
            const res = await fetch(`/pengurus/donasi-data?${params.toString()}`);
            const response = await res.json();
            renderTable(response.data, response.from || 1);
            renderPagination(response);
            updateSortIcons();
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = '';
        const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
        
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 text-muted">Belum ada program donasi.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const target = parseFloat(item.target_dana);
            const terkumpul = parseFloat(item.total_terkumpul);
            const persen = target > 0 ? Math.min(100, (terkumpul / target) * 100).toFixed(1) : 0;
            const fotoUrl = item.foto_donasi ? `/storage/${item.foto_donasi}` : 'https://via.placeholder.com/60?text=No+Img';

            // Style baris jika sudah lewat tanggal
            const isExpired = item.tanggal_selesai && new Date(item.tanggal_selesai) < new Date().setHours(0,0,0,0);
            const rowClass = isExpired ? 'bg-light text-muted' : '';
            const statusBadge = isExpired ? '<span class="badge bg-secondary ms-2">Selesai</span>' : '';

            const row = `
                <tr class="${rowClass}">
                    <td class="text-center">${startNum + i}</td>
                    <td class="text-center">
                        <img src="${fotoUrl}" class="rounded shadow-sm" style="width:50px; height:50px; object-fit:cover;">
                    </td>
                    <td>
                        <div class="fw-bold text-dark">${item.nama_donasi}</div>
                        ${statusBadge}
                    </td>
                    
                    <td class="text-center small">${formatTanggal(item.tanggal_mulai)}</td>
                    <td class="text-center small">${item.tanggal_selesai ? formatTanggal(item.tanggal_selesai) : '<span class="badge bg-success rounded-pill" style="font-size: 0.7rem;">Seumur Hidup</span>'}</td>

                    <td class="text-end fw-semibold text-secondary">${formatRupiah(target)}</td>
                    <td class="text-end fw-bold text-success">${formatRupiah(terkumpul)}</td>
                    <td class="text-center" style="width: 15%;">
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: ${persen}%"></div>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">${persen}% Terkumpul</small>
                    </td>
                    
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2"> 
                            <button class="btn btn-sm btn-info text-white rounded-3 shadow-sm" onclick="window.bukaDetail('${item.id_donasi}')" title="Detail & Pemasukan">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" onclick="window.editDonasi('${item.id_donasi}')" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger rounded-3 shadow-sm" onclick="window.hapusDonasi('${item.id_donasi}')" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    // ============================================================
    // 5. CREATE & EDIT
    // ============================================================

    // Buka Modal Tambah
    document.getElementById('btnTambahDonasi').addEventListener('click', () => {
        formDonasi.reset();
        document.getElementById('id_donasi').value = '';
        realTarget.value = ''; 
        displayTarget.value = '';
        resetPreview(); // Reset tampilan foto ke kotak dashed
        document.getElementById('modalTitle').innerText = "Program Donasi Baru";
        modalDonasi.show();
    });

    // Buka Modal Edit
    window.editDonasi = async (id) => {
        try {
            const res = await fetch(`/pengurus/donasi/${id}`);
            const data = await res.json();

            document.getElementById('id_donasi').value = data.id_donasi;
            document.getElementById('nama_donasi').value = data.nama_donasi;
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('tanggal_mulai').value = data.tanggal_mulai.split('T')[0];
            if(data.tanggal_selesai) document.getElementById('tanggal_selesai').value = data.tanggal_selesai.split('T')[0];

            realTarget.value = data.target_dana;
            displayTarget.value = new Intl.NumberFormat('id-ID').format(data.target_dana);

            // LOGIC TAMPIL FOTO SAAT EDIT
            if (data.foto_donasi) {
                showPreview(data.foto_url || `/storage/${data.foto_donasi}`);
            } else {
                resetPreview();
            }

            document.getElementById('modalTitle').innerText = "Edit Program Donasi";
            modalDonasi.show();
        } catch (error) {
            Swal.fire('Error', 'Gagal memuat data', 'error');
        }
    };

    // Submit Form
    formDonasi.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!realTarget.value && displayTarget.value) {
            realTarget.value = displayTarget.value.replace(/[^0-9]/g, '');
        }

        const id = document.getElementById('id_donasi').value;
        const formData = new FormData(formDonasi);
        let url = '/pengurus/donasi';
        if (id) { url += `/${id}`; formData.append('_method', 'PUT'); }

        try {
            const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: formData });
            const data = await res.json();
            
            if (res.ok) {
                modalDonasi.hide();
                Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', confirmButtonColor: '#198754' });
                loadDonasi();
            } else {
                throw new Error(data.message || 'Gagal menyimpan data');
            }
        } catch (err) { Swal.fire('Error', err.message, 'error'); }
    });

    // ============================================================
    // 6. DELETE & UTILS (SAMA SEPERTI SEBELUMNYA)
    // ============================================================
    
    window.hapusDonasi = async (id) => {
        const c = await Swal.fire({ 
            title: 'Hapus?', text: "Data akan hilang permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' 
        });
        if (c.isConfirmed) {
            await fetch(`/pengurus/donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            Swal.fire('Terhapus!', '', 'success');
            loadDonasi();
        }
    };

    // DETAIL & PEMASUKAN LOGIC
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
        if (data.pemasukan.length === 0) { 
            tbodyRiwayat.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada donasi masuk.</td></tr>`; 
        } else {
            data.pemasukan.forEach(p => {
                let badge = p.metode_pembayaran === 'tunai' ? '<span class="badge bg-secondary">Tunai</span>' : '<span class="badge bg-primary">Transfer</span>';
                tbodyRiwayat.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${formatTanggal(p.tanggal)}</td>
                        <td>${p.nama_donatur}<br><small class="text-muted">"${p.pesan||'-'}"</small></td>
                        <td class="text-center">${badge}</td>
                        <td class="text-end fw-bold text-success">${formatRupiah(p.nominal)}</td>
                        <td class="text-center"><button class="btn btn-sm btn-outline-danger" onclick="window.hapusPemasukan('${p.id_pemasukan_donasi}')"><i class="bi bi-trash"></i></button></td>
                    </tr>
                `);
            });
        }
    }

    // ... (kode sebelumnya) ...

    // ============================================================
    // LOGIC AUTO FORMAT RUPIAH UNTUK INPUT PEMASUKAN (BARU)
    // ============================================================
    const displayNominal = document.getElementById('display_nominal');
    const realNominal = document.getElementById('real_nominal');

    if (displayNominal) {
        displayNominal.addEventListener('keyup', function(e) {
            // 1. Ambil karakter angka saja
            let rawValue = this.value.replace(/[^0-9]/g, '');
            
            // 2. Simpan ke input hidden (untuk dikirim ke server)
            realNominal.value = rawValue;

            // 3. Format tampilan visual (kasih titik)
            if (rawValue) {
                this.value = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                this.value = '';
            }
        });
    }

    // ============================================================
    // UPDATE FORM SUBMIT (Agar validasi aman)
    // ============================================================
    formPemasukan.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Jaga-jaga jika user copas angka, pastikan hidden input terisi
        if (!realNominal.value && displayNominal.value) {
            realNominal.value = displayNominal.value.replace(/[^0-9]/g, '');
        }

        const formData = new FormData(formPemasukan);
        
        try {
            const res = await fetch('/pengurus/pemasukan-donasi', { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, 
                body: formData 
            });
            
            if (res.ok) {
                modalInputPemasukan.hide(); 
                formPemasukan.reset();
                
                // RESET MANUAL INPUT VISUAL SETELAH SUKSES
                displayNominal.value = ''; 
                realNominal.value = '';

                Swal.fire({ 
                    icon: 'success', 
                    title: 'Donasi Masuk!', 
                    timer: 1500, 
                    showConfirmButton: false 
                });
                await refreshModalDetail(); 
                loadDonasi(); 
            } else {
                Swal.fire('Gagal', 'Cek inputan nominal', 'error');
            }
        } catch (err) { Swal.fire('Gagal', '', 'error'); }
    });


    formPemasukan.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formPemasukan);
        try {
            const res = await fetch('/pengurus/pemasukan-donasi', { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: formData });
            if (res.ok) {
                modalInputPemasukan.hide(); formPemasukan.reset();
                Swal.fire({ icon: 'success', title: 'Masuk!', timer: 1500, showConfirmButton: false });
                await refreshModalDetail(); loadDonasi();
            }
        } catch (err) { Swal.fire('Gagal', '', 'error'); }
    });

    window.hapusPemasukan = async (id) => {
        const c = await Swal.fire({ title: 'Batalkan donasi?', icon: 'warning', showCancelButton: true });
        if(c.isConfirmed) {
            await fetch(`/pengurus/pemasukan-donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            await refreshModalDetail(); loadDonasi();
        }
    };

    // SEARCH, FILTER, SORT
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { state.search = e.target.value; state.page = 1; loadDonasi(); }, 300);
    });
    document.getElementById('statusFilter').addEventListener('change', (e) => {
        state.status = e.target.value; state.page = 1; loadDonasi();
    });
    document.getElementById('sortMulai').addEventListener('click', () => {
        state.sortBy = 'tanggal_mulai'; state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc'; loadDonasi();
    });
    document.getElementById('sortSelesai').addEventListener('click', () => {
        state.sortBy = 'tanggal_selesai'; state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc'; loadDonasi();
    });
    
    function updateSortIcons() {
        document.querySelectorAll('.sort-icon').forEach(i => i.className = 'bi bi-arrow-down-up small text-muted sort-icon');
        let active = state.sortDir === 'asc' ? 'bi bi-arrow-up text-primary' : 'bi bi-arrow-down text-primary';
        if(state.sortBy === 'tanggal_mulai') document.querySelector('#sortMulai i').className = `${active} small sort-icon`;
        if(state.sortBy === 'tanggal_selesai') document.querySelector('#sortSelesai i').className = `${active} small sort-icon`;
    }

    function renderPagination(response) {
        const nav = document.getElementById('paginationLinks');
        document.getElementById('paginationInfo').textContent = `Menampilkan ${response.from||0} - ${response.to||0} dari ${response.total} data`;
        let html = '<ul class="pagination justify-content-end mb-0 pagination-sm">';
        response.links.forEach(link => {
            html += `<li class="page-item ${link.active?'active':''} ${link.url?'':'disabled'}"><a class="page-link" href="#" data-url="${link.url}">${link.label.replace('&laquo; Previous','<').replace('Next &raquo;','>')}</a></li>`;
        });
        nav.innerHTML = html + '</ul>';
        nav.querySelectorAll('a.page-link').forEach(a => a.addEventListener('click', (e) => {
            e.preventDefault(); if(a.dataset.url && a.dataset.url !== 'null') {
                state.page = new URLSearchParams(a.dataset.url.split('?')[1]).get('page'); loadDonasi();
            }
        }));
    }

    loadDonasi();
});