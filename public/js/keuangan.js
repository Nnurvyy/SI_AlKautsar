document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelKeuangan tbody');
    
    // --- MODAL Elements ---
    const modalTransaksiEl = document.getElementById('modalTransaksi');
    const modalTransaksi = new bootstrap.Modal(modalTransaksiEl);
    const formTransaksi = document.getElementById('formTransaksi');
    
    const modalKategoriEl = document.getElementById('modalKategori');
    const modalKategori = new bootstrap.Modal(modalKategoriEl);
    const formKategori = document.getElementById('formKategori');
    const listKategoriContainer = document.getElementById('listKategoriContainer');
    
    // --- DETEKSI TOMBOL TAMBAH (Pemasukan vs Pengeluaran) ---
    // Script akan mencari salah satu tombol. Jika btnTambahPemasukan tidak ada, cari btnTambahPengeluaran.
    const btnTambahTransaksi = document.getElementById('btnTambahPemasukan') || document.getElementById('btnTambahPengeluaran');

    // --- State ---
    let currentPage = 1;

    const formatRupiah = (angka) => "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    const formatTanggal = (str) => new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

    // =================================================
    // 1. LOAD TRANSAKSI (Tabel Utama)
    // =================================================
    async function loadTransaksi() {
        const search = document.getElementById('searchInput').value;
        tbody.innerHTML = `<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;
        
        try {
            const res = await fetch(`${URL_API_TRANSAKSI}/data?page=${currentPage}&search=${search}`);
            const response = await res.json();
            renderTable(response.data, response.from || 1);
            renderPagination(response);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center">Belum ada data.</td></tr>`;
            return;
        }

        data.forEach((item) => {
            const kategoriName = item.kategori ? item.kategori.nama_kategori_keuangan : '-';
            const nominalClass = TIPE_HALAMAN === 'pemasukan' ? 'text-success' : 'text-danger';
            
            const row = `
                <tr>
                    <td>${formatTanggal(item.tanggal)}</td>
                    <td><span class="badge bg-secondary">${kategoriName}</span></td>
                    <td>${item.deskripsi || '-'}</td>
                    <td class="text-end fw-bold ${nominalClass}">${formatRupiah(item.nominal)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="window.editTransaksi('${item.id_keuangan}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="window.hapusTransaksi('${item.id_keuangan}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(response) {
        const infoEl = document.getElementById('paginationInfo');
        if(infoEl) {
            infoEl.textContent = `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
        }
    }

    const searchInput = document.getElementById('searchInput');
    if(searchInput) {
        searchInput.addEventListener('keyup', () => { currentPage = 1; loadTransaksi(); });
    }

    // =================================================
    // 2. TRANSAKSI (Create / Edit / Delete)
    // =================================================
    
    // LOGIKA BARU: Cek apakah tombol ada sebelum pasang Event Listener
    if (btnTambahTransaksi) {
        btnTambahTransaksi.addEventListener('click', async () => {
            formTransaksi.reset();
            document.getElementById('id_keuangan').value = '';
            document.getElementById('modalTransaksiTitle').textContent = `Tambah ${TIPE_HALAMAN === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran'}`;
            await loadDropdownKategori();
            modalTransaksi.show();
        });
    }

    formTransaksi.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('id_keuangan').value;
        const formData = new FormData(formTransaksi);
        
        let url = URL_API_TRANSAKSI;
        
        if (id) {
            url += `/${id}`;
            formData.append('_method', 'PUT'); 
        }

        try {
            const res = await fetch(url, {
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });
            
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan');

            modalTransaksi.hide();
            Swal.fire('Berhasil', data.message, 'success');
            loadTransaksi();
            
            setTimeout(() => location.reload(), 1000); 
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

    window.editTransaksi = async (id) => {
        try {
            const res = await fetch(`${URL_API_TRANSAKSI}/${id}`);
            const data = await res.json();

            await loadDropdownKategori(); 
            
            document.getElementById('id_keuangan').value = data.id_keuangan;
            document.getElementById('inputTanggal').value = data.tanggal;
            document.getElementById('selectKategori').value = data.id_kategori_keuangan;
            document.getElementById('inputNominal').value = data.nominal;
            document.getElementById('inputDeskripsi').value = data.deskripsi;

            document.getElementById('modalTransaksiTitle').textContent = "Edit Transaksi";
            modalTransaksi.show();
        } catch (err) {
            Swal.fire('Error', 'Gagal memuat data', 'error');
        }
    };

    window.hapusTransaksi = async (id) => {
        const c = await Swal.fire({ title: 'Hapus?', text: 'Data tidak bisa dikembalikan!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' });
        if (c.isConfirmed) {
            await fetch(`${URL_API_TRANSAKSI}/${id}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': token } 
            });
            loadTransaksi();
            Swal.fire('Terhapus', '', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    };

    // =================================================
    // 3. KELOLA KATEGORI (CRUD dalam Modal)
    // =================================================
    const btnKelolaKategori = document.getElementById('btnKelolaKategori');
    if(btnKelolaKategori) {
        btnKelolaKategori.addEventListener('click', () => {
            resetFormKategori();
            loadListKategori();
            modalKategori.show();
        });
    }

    async function loadListKategori() {
        listKategoriContainer.innerHTML = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm"></span></div>';
        try {
            const res = await fetch(`${URL_API_KATEGORI}/data?tipe=${TIPE_HALAMAN}`);
            const data = await res.json();
            renderListKategori(data);
        } catch (err) {
            listKategoriContainer.innerHTML = '<div class="text-danger text-center">Gagal memuat kategori.</div>';
        }
    }

    function renderListKategori(data) {
        listKategoriContainer.innerHTML = '';
        if (data.length === 0) {
            listKategoriContainer.innerHTML = '<div class="list-group-item text-center text-muted">Belum ada kategori.</div>';
            return;
        }

        data.forEach(cat => {
            const item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            item.innerHTML = `
                <span>${cat.nama_kategori_keuangan}</span>
                <div>
                    <button class="btn btn-sm btn-link text-primary p-0 me-2" onclick="window.startEditKategori('${cat.id_kategori_keuangan}', '${cat.nama_kategori_keuangan}')">Edit</button>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="window.hapusKategori('${cat.id_kategori_keuangan}')">Hapus</button>
                </div>
            `;
            listKategoriContainer.appendChild(item);
        });
    }

    async function loadDropdownKategori() {
        const res = await fetch(`${URL_API_KATEGORI}/data?tipe=${TIPE_HALAMAN}`);
        const data = await res.json();
        const select = document.getElementById('selectKategori');
        select.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        data.forEach(cat => {
            select.innerHTML += `<option value="${cat.id_kategori_keuangan}">${cat.nama_kategori_keuangan}</option>`;
        });
    }

    formKategori.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('id_kategori').value;
        const nama = document.getElementById('nama_kategori').value;
        const btnSimpan = document.getElementById('btnSimpanKategori');

        if(!nama) return;

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        let url = URL_API_KATEGORI;
        let method = 'POST';
        let body = { 
            nama_kategori_keuangan: nama, 
            tipe: TIPE_HALAMAN 
        };

        if (id) {
            url += `/${id}`;
            method = 'PUT';
        }

        try {
            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(body)
            });
            
            if (res.ok) {
                resetFormKategori();
                loadListKategori(); 
            } else {
                alert('Gagal menyimpan kategori');
            }
        } catch (err) {
            console.error(err);
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = id ? '<i class="bi bi-check-lg"></i> Update' : '<i class="bi bi-plus-lg"></i> Tambah';
        }
    });

    window.startEditKategori = (id, nama) => {
        document.getElementById('id_kategori').value = id;
        document.getElementById('nama_kategori').value = nama;
        document.getElementById('nama_kategori').focus();
        
        const btnSimpan = document.getElementById('btnSimpanKategori');
        btnSimpan.innerHTML = '<i class="bi bi-check-lg"></i> Update';
        btnSimpan.classList.replace('btn-primary', 'btn-success');
        
        document.getElementById('btnBatalEditKategori').classList.remove('d-none');
    };

    document.getElementById('btnBatalEditKategori').addEventListener('click', resetFormKategori);

    function resetFormKategori() {
        document.getElementById('id_kategori').value = '';
        document.getElementById('nama_kategori').value = '';
        
        const btnSimpan = document.getElementById('btnSimpanKategori');
        btnSimpan.innerHTML = '<i class="bi bi-plus-lg"></i> Tambah';
        btnSimpan.classList.replace('btn-success', 'btn-primary');
        
        document.getElementById('btnBatalEditKategori').classList.add('d-none');
    }

    window.hapusKategori = async (id) => {
        if(!confirm('Hapus kategori ini?')) return;

        try {
            const res = await fetch(`${URL_API_KATEGORI}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token }
            });
            if (res.ok) loadListKategori();
        } catch (err) {
            alert('Gagal menghapus');
        }
    };

    loadTransaksi();
});