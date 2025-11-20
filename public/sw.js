// GANTI VERSI SETIAP KALI ADA PERUBAHAN DI FILE BLADE/CSS/JS
const CACHE_NAME = 'laravel-pwa-v2'; 

const urlsToCache = [
  // --- 1. HALAMAN UTAMA (ROUTE LARAVEL) ---
  // Masukkan semua route yang ada di menu navigasi kamu
  '/',
  '/jadwal-kajian',
  '/jadwal-adzan',
  '/donasi',
  '/artikel',
  '/program',
  '/tabungan-qurban-saya',
  '/jadwal-khotib',

  // --- 2. FILE PWA WAJIB ---
  '/manifest.json',
  '/favicon.ico',
  // Pastikan file ini benar-benar ada di public/images/icons/
  '/images/icons/icon-192.png', 
  '/images/icons/icon-512.png',

  // --- 3. GAMBAR STATIS (DARI FOLDER PUBLIC/IMAGES/ICONS) ---
  // Berdasarkan struktur folder yang kamu kirim:
  '/images/bg-login.jpeg',
  '/images/bgpattern1.jpeg',
  '/images/masjid.jpeg',
  '/images/pembangunan-masjid.jpg',
  
  // Masukkan juga ikon navigasi (home.png, dll) jika ada di folder icons
  // (Pastikan nama filenya sesuai huruf besar/kecilnya)
  '/images/icons/home.png',
  '/images/icons/kajian.png',
  '/images/icons/adzan.png',
  '/images/icons/donasi.png',
  '/images/icons/more (1).png',
  '/images/icons/artikel.png',
  '/images/icons/program.png',
  '/images/icons/qurban.png',
  '/images/icons/khutbah-jumat.png',

  // --- 4. LIBRARY EKSTERNAL (CDN) ---
  // Agar tampilan tidak hancur (rusak) saat offline/loading
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
  
  // --- 5. CSS/JS LOKAL (JIKA ADA) ---
  // Cek folder public/js dan public/css kamu. 
  // Jika ada file 'style.css' atau 'settings.js', masukkan di sini:
  // '/css/style.css',
  '/js/settings.js' 
];

// --- EVENT INSTALL (Menyimpan file ke cache) ---
// --- EVENT INSTALL (VERSI DETEKTIF / DEBUGGING) ---
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      console.log('Mulai mengecek file satu per satu...');
      
      // Kita loop semua file untuk melihat mana yang error
      for (const url of urlsToCache) {
        try {
          const response = await fetch(url);
          if (!response.ok) {
            throw new Error(`Status: ${response.status}`);
          }
          await cache.put(url, response);
          console.log(`✅ Berhasil: ${url}`);
        } catch (error) {
          // INI DIA PELAKUNYA!
          console.error(`❌ GAGAL DOWNLOAD: ${url}`, error);
        }
      }
    })
  );
});

// --- EVENT FETCH (Mengambil dari cache biar cepat/offline) ---
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // 1. Jika ada di cache, ambil dari cache (INSTAN)
        if (response) {
          return response;
        }
        // 2. Jika tidak, ambil dari internet
        return fetch(event.request);
      })
  );
});

// --- EVENT ACTIVATE (Membersihkan cache lama) ---
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(cacheName => {
          // Hapus cache lama yang namanya beda dengan versi sekarang
          return cacheName.startsWith('laravel-pwa-') && cacheName !== CACHE_NAME;
        }).map(cacheName => {
          console.log('Menghapus cache lama:', cacheName);
          return caches.delete(cacheName);
        })
      );
    })
  );
});