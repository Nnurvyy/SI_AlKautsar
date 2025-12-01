document.addEventListener("DOMContentLoaded", function () {
    // Cek apakah elemen ada sebelum dijalankan (karena JS ini diload di index)
    const tableBody = document.querySelector("#tabelartikel tbody");
    if (!tableBody) return; // Stop jika tidak ada tabel (misal di halaman lain)

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const statusFilter = document.getElementById("statusFilter");
    const searchInput = document.getElementById("searchInput");
    const paginationLinks = document.getElementById("paginationLinks");
    const paginationInfo = document.getElementById("paginationInfo");

    const modalDetailEl = document.getElementById('modalDetailArtikel');
    const modalDetail = new bootstrap.Modal(modalDetailEl);

    loadArtikel();

    // --- FORMATTER TANGGAL ---
    function formatTanggalIndo(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '-'; 
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        }).format(date);
    }

    // --- LOAD DATA ---
    function loadArtikel(page = 1) {
        const status = statusFilter ? statusFilter.value : 'all';
        const search = searchInput ? searchInput.value : '';

        // Loading State
        let colCount = tableBody.closest('table').querySelector('thead tr').cells.length;
        tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success" role="status"></div></td></tr>`;

        fetch(`/pengurus/artikel-data?page=${page}&status=${status}&search=${search}`)
            .then(res => res.json())
            .then(res => renderData(res))
            .catch(err => console.error(err));
    }

    // --- RENDER TABEL ---
    function renderData(res) {
        const tableBody = document.querySelector("#tabelartikel tbody"); // Pastikan selector ini
        const paginationInfo = document.getElementById("paginationInfo");
        const paginationLinks = document.getElementById("paginationLinks");

        tableBody.innerHTML = "";
        let no = res.from;

        if (res.data.length === 0) {
            let colCount = tableBody.closest('table').querySelector('thead tr').cells.length;
            tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5 text-muted">Belum ada data artikel.</td></tr>`;
            paginationInfo.textContent = "";
            paginationLinks.innerHTML = "";
            return;
        }

        res.data.forEach(item => {
            // Badge Status
            let statusBadge = item.status_artikel === 'published' 
                ? '<span class="badge rounded-pill bg-success px-3">Published</span>' 
                : '<span class="badge rounded-pill bg-secondary px-3">Draft</span>';
            
            // Foto Default
            let foto = item.foto_url 
                ? `<img src="${item.foto_url}" class="rounded-3 shadow-sm border" style="width:50px; height:50px; object-fit: cover;">`
                : `<div class="rounded-3 bg-light d-flex align-items-center justify-content-center border" style="width:50px; height:50px;"><i class="bi bi-image text-muted"></i></div>`;

            // --- PERUBAHAN DI SINI (STYLE BUTTON) ---
            tableBody.innerHTML += `
                <tr>
                    <td class="text-center fw-bold text-muted">${no++}</td>
                    <td class="text-center">${foto}</td>
                    <td>
                        <div class="fw-bold text-dark">${item.judul_artikel}</div>
                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">${item.penulis_artikel}</small>
                    </td>
                    <td>${item.penulis_artikel}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center small">${formatTanggalIndo(item.tanggal_terbit_artikel)}</td>
                    
                    <td class="text-center">            
                        <div class="d-flex justify-content-center gap-2"> <button onclick="lihatDetailArtikel('${item.id_artikel}')" 
                                class="btn btn-sm btn-info text-white rounded-3 shadow-sm" 
                                title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>

                            <a href="/pengurus/artikel/${item.id_artikel}/edit" 
                                class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" 
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <button onclick="hapusArtikel('${item.id_artikel}')" 
                                class="btn btn-sm btn-danger rounded-3 shadow-sm" 
                                title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>

                        </div>
                    </td>
                </tr>`;
        });

        paginationInfo.textContent = `Menampilkan ${res.data.length} dari ${res.total} data`;
        renderPagination(res);
    }

    // --- RENDER PAGINATION ---
    function renderPagination(res) {
        let html = `<ul class="pagination pagination-sm mb-0">`;
        if (res.prev_page_url) {
            html += `<li class="page-item"><button class="page-link text-success" data-page="${res.current_page - 1}"><i class="bi bi-chevron-left"></i></button></li>`;
        }
        if (res.next_page_url) {
            html += `<li class="page-item"><button class="page-link text-success" data-page="${res.current_page + 1}"><i class="bi bi-chevron-right"></i></button></li>`;
        }
        html += `</ul>`;
        paginationLinks.innerHTML = html;

        paginationLinks.querySelectorAll(".page-link").forEach(link => {
            link.addEventListener("click", function () {
                loadArtikel(this.getAttribute("data-page"));
            });
        });
    }

    // --- FUNGSI GLOBAL (window) ---

    // 1. Lihat Detail
    window.lihatDetailArtikel = function(id) {
        fetch(`/pengurus/artikel-data/${id}`)
            .then(res => {
                if(!res.ok) throw new Error("Gagal mengambil data");
                return res.json();
            })
            .then(data => {
                document.getElementById("detailJudulArtikel").innerText = data.judul_artikel;
                document.getElementById("d_penulis").innerText = data.penulis_artikel;
                document.getElementById("d_status_artikel").innerText = data.status_artikel === 'published' ? 'Published' : 'Draft';
                document.getElementById("d_tanggal_terbit").innerText = formatTanggalIndo(data.tanggal_terbit_artikel);
                document.getElementById("d_isi").innerHTML = data.isi_artikel; // Render HTML dari Quill
                
                // Handle Foto Detail
                const imgEl = document.getElementById("detailFotoArtikel");
                if(data.foto_url) {
                    imgEl.src = data.foto_url;
                    imgEl.classList.remove('d-none');
                } else {
                    imgEl.classList.add('d-none');
                }

                modalDetail.show();
            })
            .catch(err => Swal.fire('Error', err.message, 'error'));
    }

    // 2. Hapus Artikel
    window.hapusArtikel = async function(id_artikel) {
        const confirm = await Swal.fire({
            title: 'Hapus Artikel?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                loadArtikel(); 
            } else {
                throw new Error(data.message || 'Terjadi kesalahan.');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };

    // Event Listeners
    if(statusFilter) statusFilter.addEventListener("change", () => loadArtikel());
    if(searchInput) searchInput.addEventListener("keyup", () => loadArtikel());
});