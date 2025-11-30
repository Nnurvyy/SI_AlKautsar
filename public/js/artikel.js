document.addEventListener("DOMContentLoaded", function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const statusFilter = document.getElementById("statusFilter");
    const searchInput = document.getElementById("searchInput");
    const tableBody = document.querySelector("#tabelartikel tbody");
    const paginationLinks = document.getElementById("paginationLinks");
    const paginationInfo = document.getElementById("paginationInfo");

    const modalDetailEl = document.getElementById('modalDetailArtikel');
    const modalDetail = new bootstrap.Modal(modalDetailEl);

    loadArtikel();

    // --- FUNGSI FORMATTER TANGGAL (BARU) ---
    // Mengubah "2025-11-30" menjadi "30 Nov 2025" atau "06 Des 2025"
    function formatTanggalIndo(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        
        // Cek validitas tanggal
        if (isNaN(date.getTime())) return '-'; 

        // Format ke Indonesia: dd MMM yyyy (contoh: 06 Des 2025)
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short', // short = Des, long = Desember
            year: 'numeric'
        }).format(date);
    }

    function loadArtikel(page = 1) {
        const status = statusFilter ? statusFilter.value : 'all';
        const search = searchInput ? searchInput.value : '';

        if(tableBody) {
             let colCount = tableBody.closest('table').querySelector('thead tr').cells.length;
             tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;
        }

        fetch(`/pengurus/artikel-data?page=${page}&status=${status}&search=${search}`)
            .then(res => res.json())
            .then(res => renderData(res))
            .catch(err => console.error(err));
    }

    function renderData(res) {
        tableBody.innerHTML = "";
        let no = res.from;

        if (res.data.length === 0) {
            let colCount = tableBody.closest('table').querySelector('thead tr').cells.length;
            tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Data tidak ditemukan.</td></tr>`;
            paginationInfo.textContent = "Menampilkan 0 data";
            paginationLinks.innerHTML = "";
            return;
        }

        res.data.forEach(item => {
            let statusBadge = item.status_artikel === 'published' 
                ? '<span class="badge bg-success">Published</span>' 
                : '<span class="badge bg-secondary">Draft</span>';

            tableBody.innerHTML += `
                <tr>
                    <td class="text-center">${no++}</td>
                    <td class="text-center">
                        <img src="${item.foto_url}" class="rounded shadow-sm" style="width:50px; height:50px; object-fit: cover;" alt="Foto">
                    </td>
                    <td class="text-center">${item.judul_artikel}</td>
                    <td class="text-center">${item.penulis_artikel}</td>
                    <td class="text-center">${statusBadge}</td>
                    
                    <td class="text-center">${formatTanggalIndo(item.tanggal_terbit_artikel)}</td>
                    
                    <td class="text-center aksi-col">            
                        <button onclick="lihatDetailArtikel('${item.id_artikel}')" class="btn btn-info btn-sm text-white me-1">
                            <i class="bi bi-eye"></i>
                        </button>
                        <a href="/pengurus/artikel/${item.id_artikel}/edit" class="btn btn-warning btn-sm me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <button onclick="hapusArtikel('${item.id_artikel}')" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        paginationInfo.textContent = `Menampilkan ${res.data.length} dari ${res.total} data`;
        renderPagination(res);
    }

    // ... (fungsi renderPagination tetap sama) ...
    function renderPagination(res) {
        let html = `<ul class="pagination mb-0">`;
        if (res.prev_page_url) {
            html += `<li class="page-item"><button class="page-link" data-page="${res.current_page - 1}">Sebelumnya</button></li>`;
        }
        if (res.next_page_url) {
            html += `<li class="page-item"><button class="page-link" data-page="${res.current_page + 1}">Berikutnya</button></li>`;
        }
        html += `</ul>`;
        paginationLinks.innerHTML = html;

        paginationLinks.querySelectorAll(".page-link").forEach(link => {
            link.addEventListener("click", function () {
                loadArtikel(this.getAttribute("data-page"));
            });
        });
    }

    // --- Window Functions ---

    window.lihatDetailArtikel = function(id) {
        fetch(`/pengurus/artikel-data/${id}`)
            .then(res => {
                if(!res.ok) throw new Error("Gagal mengambil data");
                return res.json();
            })
            .then(data => {
                document.getElementById("detailJudulArtikel").innerText = data.judul_artikel;
                document.getElementById("d_penulis").innerText = data.penulis_artikel;
                document.getElementById("d_status_artikel").innerText = data.status_artikel;
                
                // MENGGUNAKAN FORMATTER BARU DI MODAL JUGA
                document.getElementById("d_tanggal_terbit").innerText = formatTanggalIndo(data.tanggal_terbit_artikel);
                
                document.getElementById("d_isi").innerHTML = data.isi_artikel;
                document.getElementById("detailFotoArtikel").src = data.foto_url;

                modalDetail.show();
            })
            .catch(err => Swal.fire('Error', err.message, 'error'));
    }

    window.hapusArtikel = async function(id_artikel) {
        // ... (Logika hapus tetap sama) ...
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

            const res = await fetch(`/pengurus/artikel/${id_artikel}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadArtikel(); 
            } else {
                throw new Error(data.message || 'Terjadi kesalahan.');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };

    if(statusFilter) statusFilter.addEventListener("change", () => loadArtikel());
    if(searchInput) searchInput.addEventListener("keyup", () => loadArtikel());
});