document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen ---
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelProgram tbody');

    // Modal Program (Create/Edit)
    const modalProgramEl = document.getElementById('modalProgram');
    const modalProgram = new bootstrap.Modal(modalProgramEl);
    const form = document.getElementById('formProgram');

    // Modal Detail
    const modalDetailEl = document.getElementById('modalDetailProgram');
    const modalDetail = new bootstrap.Modal(modalDetailEl);

    // Elemen Foto (UI Baru)
    const fotoInput = document.getElementById('foto_program'); 
    const uploadPlaceholder = document.getElementById('uploadPlaceholder'); // Kotak dashed
    const previewContainer = document.getElementById('previewContainer'); // Wrapper Preview
    const preview = document.getElementById('previewFoto'); // Img Preview
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    
    // Filter & Search
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');

    // State Management
    let state = {
        currentPage: 1, status: 'all', search: '', perPage: 10, sortBy: 'tanggal_program', sortDir: 'desc', searchTimeout: null
    };

    // Helper: Reset Modal ke kondisi awal
    modalProgramEl.addEventListener('hidden.bs.modal', function () {
        form.reset();
        document.getElementById('id_program').value = ''; 
        resetPreview(); // Reset tampilan foto ke kotak dashed
    });

    // --- LOGIC PREVIEW FOTO (SWAP CONTAINER) ---
    function showPreview(src) {
        preview.src = src;
        uploadPlaceholder.classList.add('d-none');
        previewContainer.classList.remove('d-none');
    }

    function resetPreview() {
        fotoInput.value = '';
        preview.src = '';
        uploadPlaceholder.classList.remove('d-none');
        previewContainer.classList.add('d-none');
    }

    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => showPreview(ev.target.result);
                reader.readAsDataURL(file);
            }
        });
    }

    if (btnHapusFoto) {
        btnHapusFoto.addEventListener('click', () => resetPreview());
    }

    // --- TOMBOL TAMBAH PROGRAM ---
    const btnTambah = document.getElementById('btnTambahProgram');
    if(btnTambah) {
        btnTambah.addEventListener('click', () => {
            form.reset();
            document.getElementById('id_program').value = '';
            resetPreview();
            document.getElementById('modalTitle').innerText = "Program Kegiatan Baru";
            modalProgram.show();
        });
    }

    // --- SUBMIT FORM ---
    form.addEventListener('submit', async e => {
        e.preventDefault();
        
        const id = document.getElementById('id_program').value; 
        const formData = new FormData(form);
        const url = id ? `/pengurus/program/${id}` : '/pengurus/program';
        if (id) formData.append('_method', 'PUT');

        try {
            const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: formData });
            const data = await res.json();
            
            if (res.ok) {
                Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', confirmButtonColor: '#198754' });
                modalProgram.hide(); 
                loadProgram(); 
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) { Swal.fire('Gagal', err.message, 'error'); }
    });

    // --- LOAD DATA TABLE ---
    async function loadProgram() { 
        let colCount = document.querySelector('#tabelProgram thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success"></div></td></tr>`;

        const url = `/pengurus/program-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

        try {
            const res = await fetch(url);
            const response = await res.json();
            renderTable(response.data, response.from || 1);
            renderPagination(response); 
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = ''; 
        if (data.length === 0) {
            let colCount = document.querySelector('#tabelProgram thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 text-muted">Belum ada program.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            let badgeClass = 'bg-secondary';
            if(item.status_program === 'sedang berjalan') badgeClass = 'bg-warning text-dark';
            if(item.status_program === 'belum dilaksanakan') badgeClass = 'bg-success';

            const row = `
                <tr>
                    <td class="text-center">${startNum + i}</td>
                    <td class="text-center">
                        <img src="${item.foto_url}" class="rounded shadow-sm" style="width:50px; height:50px; object-fit: cover;">
                    </td>
                    <td><div class="fw-bold text-dark">${item.nama_program}</div></td>
                    <td class="text-center small">${formatTanggal(item.tanggal_program)}</td>
                    <td class="text-center small">${item.lokasi_program}</td>
                    <td class="text-center"><span class="badge ${badgeClass} rounded-pill px-3 fw-normal">${item.status_program}</span></td>
                    <td class="text-center">
                         <div class="d-flex justify-content-center gap-2"> 
                            <button class="btn btn-sm btn-info text-white rounded-3 shadow-sm" onclick="window.showDetailProgram('${item.id_program}')"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" onclick="window.editProgram('${item.id_program}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger rounded-3 shadow-sm" onclick="window.hapusProgram('${item.id_program}')"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    // --- EDIT & DELETE (Global Functions) ---
    window.editProgram = async function(id) {
        try {
            const res = await fetch(`/pengurus/program/${id}`);
            const data = await res.json();

            document.getElementById('id_program').value = data.id_program;
            document.getElementById('nama_program').value = data.nama_program;
            document.getElementById('penyelenggara_program').value = data.penyelenggara_program;
            document.getElementById('lokasi_program').value = data.lokasi_program;
            document.getElementById('deskripsi_program').value = data.deskripsi_program;
            document.getElementById('status_program').value = data.status_program;
            if (data.tanggal_program) document.getElementById('tanggal_program').value = data.tanggal_program.substring(0, 16);

            // Logic Foto
            if (data.foto_program) {
                showPreview(data.foto_url);
            } else {
                resetPreview();
            }

            document.getElementById('modalTitle').innerText = "Edit Program Kegiatan";
            modalProgram.show();
        } catch (err) { Swal.fire('Error', 'Gagal memuat data', 'error'); }
    };

    window.hapusProgram = async function(id) {
        const c = await Swal.fire({ title: 'Hapus?', text: 'Data akan hilang permanen!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' });
        if (c.isConfirmed) {
            await fetch(`/pengurus/program/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            Swal.fire('Terhapus!', '', 'success');
            loadProgram();
        }
    };

    window.showDetailProgram = async function(id) {
        try {
            const res = await fetch(`/pengurus/program/${id}`);
            const data = await res.json();
            
            document.getElementById('d_nama').textContent = data.nama_program;
            document.getElementById('d_penyelenggara').textContent = data.penyelenggara_program;
            document.getElementById('d_lokasi').textContent = data.lokasi_program;
            document.getElementById('d_tanggal').textContent = formatTanggal(data.tanggal_program);
            document.getElementById('d_deskripsi').textContent = data.deskripsi_program;
            document.getElementById('detailFotoProgram').src = data.foto_url || '/images/default_program.png';
            
            // Badge Status di Detail
            let badgeClass = 'bg-secondary';
            if(data.status_program === 'sedang berjalan') badgeClass = 'bg-warning text-dark';
            if(data.status_program === 'belum dilaksanakan') badgeClass = 'bg-success';
            const statusEl = document.getElementById('d_status');
            statusEl.className = `badge rounded-pill px-3 py-2 mb-2 ${badgeClass}`;
            statusEl.textContent = data.status_program;

            modalDetail.show();
        } catch (err) { Swal.fire('Error', 'Gagal memuat detail', 'error'); }
    };

    // --- UTILS: Format Date & Pagination ---
    function formatTanggal(str) {
        if (!str) return '-';
        return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' });
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
            e.preventDefault(); 
            if(a.dataset.url && a.dataset.url !== 'null') {
                state.currentPage = new URLSearchParams(a.dataset.url.split('?')[1]).get('page'); 
                loadProgram();
            }
        }));
    }

    // --- Search & Filter ---
    searchInput.addEventListener('keyup', () => { clearTimeout(state.searchTimeout); state.searchTimeout = setTimeout(() => { state.search = searchInput.value; state.currentPage = 1; loadProgram(); }, 300); });
    statusFilter.addEventListener('change', () => { state.status = statusFilter.value; state.currentPage = 1; loadProgram(); });

    // Init
    loadProgram();
});