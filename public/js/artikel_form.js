document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. Definisi Variabel DOM Utama ---
    const editorContainer = document.getElementById('editor-container');
    const isiArtikelInput = document.getElementById('isiArtikelInput'); // Input Hidden untuk konten
    const form = document.getElementById('artikelForm');
    const fotoInput = document.getElementById('foto_artikel');
    const fotoPreview = document.getElementById('fotoPreview');
    
    // ------------------------------------------------------------------
    // A. INISIALISASI QUILL.JS & FORM SUBMISSION LOGIC
    // ------------------------------------------------------------------
    
    // Pastikan elemen editor dan form ditemukan sebelum inisialisasi
    if (editorContainer && isiArtikelInput && form) {
        
        // Inisialisasi Quill
        const quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: 'Tulis konten artikel di sini...',
            modules: {
                // Konfigurasi Toolbar
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], 
                    [{ 'header': [1, 2, 3, 4, false] }], // Ukuran heading
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }], // List
                    [{ 'align': [] }], // Justifikasi teks
                    ['link', 'image', 'code-block'], // Link, Image, Code
                    ['clean'] // Hapus format
                ]
            }
        });

        // Event Listener untuk Form Submission (PENTING untuk mengambil konten Quill)
        form.addEventListener('submit', function (e) {
            
            // Dapatkan konten HTML yang dihasilkan oleh editor Quill
            // .ql-editor adalah class yang otomatis dibuat oleh Quill
            const htmlContent = editorContainer.querySelector('.ql-editor').innerHTML;
            
            // Masukkan konten HTML ke input hidden (isi_artikel)
            // Nilai inilah yang akan dikirim ke Controller (Request::input('isi_artikel'))
            isiArtikelInput.value = htmlContent;

            // Jika konten kosong (hanya tag <p><br></p>), Anda bisa mencegah submission di sini:
            // if (htmlContent === '<p><br></p>' || htmlContent.trim() === '') {
            //     alert('Isi artikel tidak boleh kosong.');
            //     e.preventDefault(); 
            // }
        });
    }

    // ------------------------------------------------------------------
    // B. PHOTO PREVIEW LOGIC
    // ------------------------------------------------------------------

    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Gunakan FileReader untuk membaca file dan menampilkan preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    fotoPreview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});