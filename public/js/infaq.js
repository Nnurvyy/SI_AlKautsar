document.addEventListener('DOMContentLoaded', () => {

    

    const form = document.getElementById('formTambahInfaq');
    const modalTambahInfaqElement = document.getElementById('modaltambahinfaq');
    const modalTambahInfaq = new bootstrap.Modal(modalTambahInfaqElement);
    const modalTitle = document.getElementById('modalInfaqLabel');

    
    const nominalInput = document.getElementById('nominal_infaq');

    
    const tbody = document.querySelector('#tabelKhotib tbody'); 
    const searchInput = document.getElementById('searchInput');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const tambahButton = document.querySelector('[data-bs-target="#modaltambahinfaq"]');
    
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    
    let state = {
        currentPage: 1,
        search: '',
        perPage: 10,
        sortBy: 'tanggal', 
        sortDir: 'desc',        
        searchTimeout: null     
    };

    

    
    if (nominalInput) {
        nominalInput.addEventListener('keyup', function(e) {
            
            let value = this.value.replace(/[^0-9]/g, ''); 
            
            
            if (value) {
                let formatted = new Intl.NumberFormat('id-ID').format(value);
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
    }

    
    function formatRupiahDisplay(angka) {
        if (!angka) return '';
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    
    function cleanRupiah(formattedValue) {
        return formattedValue.replace(/\./g, ''); 
    }

    

    function setLoading(isLoading) {
        if (!submitButton) return;
        if (isLoading) {
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';
        const date = new Date(tanggalStr); 
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            : '-';
    }

    

    
    async function loadInfaq() {
        if (!tbody) return;
        
        let colCount = 4;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        const url = `/pengurus/infaq-jumat-data?page=${state.currentPage}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal memuat data');
            const response = await res.json();
            
            renderTable(response.data, response.from || 1);
            renderPagination(response); 
            
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">${err.message}</td></tr>`;
            paginationInfo.textContent = '';
            paginationContainer.innerHTML = '';
        }
    }
    
    
    function renderTable(data, startingNumber) {
        tbody.innerHTML = ''; 
        
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center">Belum ada data.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const row = `
                <tr>
                    <td class="text-center">${startingNumber + i}</td>
                    <td class="text-center">${formatTanggal(item.tanggal_infaq)}</td>
                    <td class="text-center fw-bold text-success">Rp ${formatRupiahDisplay(item.nominal_infaq)}</td>   
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm" onclick="editInfaq('${item.id_infaq_jumat}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="hapusInfaq('${item.id_infaq_jumat}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            const id = document.getElementById('id_infaq').value;
            const formData = new FormData(form);

            
            
            const rawNominal = cleanRupiah(document.getElementById('nominal_infaq').value);
            formData.set('nominal_infaq', rawNominal); 
            
            const url = id ? `/pengurus/infaq-jumat/${id}` : '/pengurus/infaq-jumat';
            if (id) formData.append('_method', 'PUT');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: formData
                });

                const data = await res.json();
                if (res.ok) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    modalTambahInfaq.hide();
                    loadInfaq(); 
                } else {
                    if (res.status === 422 && data.errors) {
                        let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (err) {
                Swal.fire('Gagal', err.message, 'error');
            } finally {
                setLoading(false);
            }
        });
    }

    
    window.editInfaq = async function(id_infaq) {
        try {
            const res = await fetch(`/pengurus/infaq-jumat/${id_infaq}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            document.getElementById('id_infaq').value = data.id_infaq_jumat; 
            document.getElementById('tanggal_infaq').value = data.tanggal_infaq;
            
            
            document.getElementById('nominal_infaq').value = formatRupiahDisplay(data.nominal_infaq);
            
            modalTitle.textContent = 'Ubah Infaq Jumat';
            modalTambahInfaq.show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    
    window.hapusInfaq = async function(id_infaq) {
        const confirmResult = await Swal.fire({
            title: 'Hapus Data?',
            text: 'Data infaq akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!confirmResult.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`/pengurus/infaq-jumat/${id_infaq}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadInfaq(); 
            } else {
                throw new Error(data.message);
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1;
                loadInfaq();
            }, 300); 
        });
    }

    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link'); 
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) return;

            const url = target.getAttribute('data-page-url'); 
            if (url) {
                const urlObj = new URL(url);
                const page = urlObj.searchParams.get('page'); 
                if (page) {
                    state.currentPage = parseInt(page);
                    loadInfaq();
                }
            }
        });
    }
    
    
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
            if (label.includes('Previous')) label = '<';
            else if (label.includes('Next')) label = '>';
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';
            linksHtml += `<li class="page-item ${disabled} ${active}"><a class="page-link" href="#" data-page-url="${link.url}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    
    if (modalTambahInfaqElement) {
        modalTambahInfaqElement.addEventListener('hidden.bs.modal', function () {
            form.reset();
            document.getElementById('id_infaq').value = '';
            modalTitle.textContent = 'Tambah Infaq Jumat';
            setLoading(false); 
        });
    }
    
    if (tambahButton) {
        tambahButton.addEventListener('click', () => {
            form.reset();
            document.getElementById('id_infaq').value = ''; 
            modalTitle.textContent = 'Tambah Infaq Jumat';
        });
    }

    
    loadInfaq();
});