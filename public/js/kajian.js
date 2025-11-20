document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formKajian');
    const modalElement = document.getElementById('modalKajian');
    const modalKajian = new bootstrap.Modal(modalElement);
    const tbody = document.querySelector('#tabelKajian tbody');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Filter
    const jenisFilter = document.getElementById('jenisFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    
    // Input Form
    const fotoInput = document.getElementById('foto_penceramah');
    const preview = document.getElementById('previewFoto');

    let state = {
        page: 1,
        jenis: 'semua',
        status: 'aktif',
        search: '',
        searchTimeout: null
    };

    // --- LOAD DATA ---
    async function loadKajian() {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center p-4">Loading...</td></tr>';
        
        const url = `/pengurus/kajian-data?page=${state.page}&jenis=${state.jenis}&status=${state.status}&search=${state.search}`;
        
        try {
            const res = await fetch(url);
            const response = await res.json();
            
            tbody.innerHTML = '';
            if(response.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center p-4 text-muted">Belum ada data kajian.</td></tr>';
                document.getElementById('paginationInfo').innerText = 'Menampilkan 0 data';
                document.getElementById('paginationLinks').innerHTML = '';
                return;
            }

            response.data.forEach((item, index) => {
                // Tampilkan Badge Jenis
                let badge = item.jenis_kajian === 'event' 
                    ? '<span class="badge bg-info text-dark">Event Besar</span>'
                    : '<span class="badge bg-secondary">Harian</span>';

                let row = `
                    <tr>
                        <td class="text-center">${response.from + index}</td>
                        <td class="text-center"><img src="${item.foto_url}" style="width:50px;height:50px;object-fit:cover;" class="rounded shadow-sm"></td>
                        <td class="fw-bold">${item.nama_penceramah}</td>
                        <td>${item.tema_kajian}</td>
                        <td class="text-center">${badge}</td>
                        <td class="text-center">${item.waktu_kajian ? item.waktu_kajian.substring(0,5) : '-'}</td>
                        <td class="text-center">${new Date(item.tanggal_kajian).toLocaleDateString('id-ID')}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning text-white" onclick="editKajian('${item.id_kajian}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="hapusKajian('${item.id_kajian}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            renderPagination(response);

        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger p-4">Gagal memuat data. Cek koneksi.</td></tr>';
        }
    }

    function renderPagination(response) {
        document.getElementById('paginationInfo').innerText = `Menampilkan ${response.from}-${response.to} dari ${response.total} data`;
        
        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        response.links.forEach(link => {
            let label = link.label.replace('&laquo; Previous', '<').replace('Next &raquo;', '>');
            let active = link.active ? 'active' : '';
            let disabled = !link.url ? 'disabled' : '';
            linksHtml += `<li class="page-item ${active} ${disabled}"><a class="page-link" href="#" data-url="${link.url}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        
        const nav = document.getElementById('paginationLinks');
        nav.innerHTML = linksHtml;
        
        nav.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                if(a.dataset.url && a.dataset.url !== 'null') {
                    const url = new URL(a.dataset.url);
                    state.page = url.searchParams.get('page');
                    loadKajian();
                }
            });
        });
    }

    // --- EVENT LISTENERS ---
    jenisFilter.addEventListener('change', (e) => { state.jenis = e.target.value; state.page = 1; loadKajian(); });
    statusFilter.addEventListener('change', (e) => { state.status = e.target.value; state.page = 1; loadKajian(); });
    searchInput.addEventListener('keyup', () => {
        clearTimeout(state.searchTimeout);
        state.searchTimeout = setTimeout(() => { state.search = searchInput.value; state.page = 1; loadKajian(); }, 300);
    });

    // --- SIMPAN DATA ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const id = document.getElementById('id_kajian').value;
        const url = id ? `/pengurus/kajian/${id}` : '/pengurus/kajian';
        if(id) formData.append('_method', 'PUT');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                body: formData
            });
            const data = await res.json();
            
            if(res.ok) {
                Swal.fire('Berhasil', data.message, 'success');
                modalKajian.hide();
                loadKajian();
            } else {
                Swal.fire('Gagal', data.message || 'Periksa inputan anda', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        }
    });

    // --- FOTO PREVIEW ---
    fotoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if(file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }
    });

    // --- EDIT ---
    window.editKajian = async (id) => {
        try {
            const res = await fetch(`/pengurus/kajian/${id}`);
            const data = await res.json();
            
            document.getElementById('id_kajian').value = data.id_kajian;
            document.getElementById('nama_penceramah').value = data.nama_penceramah;
            document.getElementById('tema_kajian').value = data.tema_kajian;
            document.getElementById('tanggal_kajian').value = data.tanggal_kajian;
            document.getElementById('waktu_kajian').value = data.waktu_kajian ? data.waktu_kajian.substring(0,5) : '';

            // Isi Radio Button Saat Edit
            if(data.jenis_kajian === 'event') {
                document.getElementById('jenis_event').checked = true;
            } else {
                document.getElementById('jenis_harian').checked = true;
            }

            if(data.foto_penceramah) {
                preview.src = data.foto_url;
                preview.classList.remove('d-none');
            } else {
                preview.classList.add('d-none');
            }

            modalKajian.show();
        } catch(err) {
            console.error(err);
        }
    };

    // --- HAPUS ---
    window.hapusKajian = async (id) => {
        const result = await Swal.fire({
            title: 'Yakin hapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        });

        if(result.isConfirmed) {
            try {
                await fetch(`/pengurus/kajian/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' }
                });
                Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                loadKajian();
            } catch(err) {
                Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
            }
        }
    };

    // Reset form
    modalElement.addEventListener('hidden.bs.modal', () => {
        form.reset();
        document.getElementById('id_kajian').value = '';
        preview.classList.add('d-none');
        document.getElementById('jenis_harian').checked = true;
    });

    loadKajian();
});