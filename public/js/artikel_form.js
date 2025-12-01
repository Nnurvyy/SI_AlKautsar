document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. SETUP QUILL EDITOR (WYSIWYG)
    // ==========================================
    // Cek apakah elemen editor ada (untuk menghindari error di halaman lain)
    if (document.getElementById('editor-container')) {
        
        var toolbarOptions = [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['link', 'clean'] // 'image' kita hapus dari toolbar karena upload foto terpisah
        ];

        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Tulis konten artikel yang menarik di sini...'
        });

        // Tangkap Form saat Submit
        var form = document.getElementById('artikelForm');
        form.onsubmit = function(e) {
            // Salin HTML dari Quill ke Input Hidden
            var isiInput = document.getElementById('isiArtikelInput');
            isiInput.value = quill.root.innerHTML;
            
            // Validasi Sederhana: Cek jika cuma tag kosong
            // (Mengatasi isu user cuma tekan spasi/enter)
            var textOnly = quill.getText().trim();
            if (textOnly.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konten Kosong',
                    text: 'Silakan tulis isi artikel terlebih dahulu!'
                });
                e.preventDefault(); // Batalkan submit
                return false;
            }
        };
    }

    // ==========================================
    // 2. LOGIC UPLOAD FOTO (Preview & Delete)
    // ==========================================
    const fotoInput = document.getElementById('foto_artikel');
    const previewContainer = document.getElementById('previewContainer');
    const fotoPreview = document.getElementById('fotoPreview');
    const btnClear = document.getElementById('clearFile');
    const inputHapus = document.getElementById('hapus_foto_input');

    // Pastikan elemen ada sebelum menjalankan logic
    if (fotoInput && previewContainer && fotoPreview) {

        // A. SAAT FILE DIPILIH (CHANGE)
        fotoInput.addEventListener('change', function(e) {
            const file = this.files[0];

            if (file) {
                // 1. Validasi Ukuran (Max 2MB)
                if(file.size > 2 * 1024 * 1024) {
                    Swal.fire('File Terlalu Besar', 'Ukuran maksimal foto adalah 2MB', 'error');
                    this.value = ''; // Reset input
                    return;
                }

                // 2. Validasi Tipe File (Optional, untuk keamanan extra di frontend)
                if(!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)){
                    Swal.fire('Format Salah', 'Harap upload file gambar (JPG/PNG)', 'warning');
                    this.value = '';
                    return;
                }

                // 3. Tampilkan Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.src = e.target.result;
                    previewContainer.classList.remove('d-none'); // Munculkan gambar
                    
                    // Pastikan flag hapus dimatikan (karena user upload baru)
                    inputHapus.value = '0'; 
                }
                reader.readAsDataURL(file);
            }
        });

        // B. SAAT TOMBOL SILANG (X) DIKLIK
        if(btnClear) {
            btnClear.addEventListener('click', function() {
                // 1. Reset Input File (agar user bisa pilih file yg sama lagi jika mau)
                fotoInput.value = '';
                
                // 2. Sembunyikan Preview
                previewContainer.classList.add('d-none');
                fotoPreview.src = '';

                // 3. Nyalakan Flag Hapus (agar Controller tahu foto lama harus dihapus)
                inputHapus.value = '1';
            });
        }
    }
});