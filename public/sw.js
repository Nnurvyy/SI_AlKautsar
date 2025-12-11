// --- GANTI VERSI SETIAP KALI ADA PERUBAHAN KODE ---
const CACHE_NAME = 'laravel-pwa-v3'; 

const urlsToCache = [
  // --- HANYA FILE ASET STATIS ---
  '/manifest.json',
  '/favicon.ico',
  '/images/icons/icon-192.png', 
  '/images/icons/icon-512.png',

  // Gambar Statis
  '/images/bg-login.jpeg',
  '/images/bgpattern1.jpeg',
  '/images/masjid.jpeg',
  '/images/pembangunan-masjid.jpg',
  
  // Ikon Navigasi
  '/images/icons/home.png',
  '/images/icons/kajian.png',
  '/images/icons/adzan.png',
  '/images/icons/donasi.png',
  '/images/icons/more (1).png',
  '/images/icons/artikel.png',
  '/images/icons/program.png',
  '/images/icons/qurban.png',
  '/images/icons/khutbah-jumat.png',

  // Library Eksternal (CDN) - Agar tampilan tetap bagus saat offline
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
  'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js',
  
  // JS/CSS Lokal
  '/js/settings.js' 
];

// --- 1. EVENT INSTALL: Cache Aset Statis Saja ---
self.addEventListener('install', event => {
  self.skipWaiting(); // Paksa SW baru langsung aktif
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      console.log('[SW] Caching static assets...');
      for (const url of urlsToCache) {
        try {
          const response = await fetch(url);
          if (response.ok) {
            await cache.put(url, response);
          } else {
            console.warn(`[SW] Gagal cache (Status ${response.status}): ${url}`);
          }
        } catch (error) {
          console.error(`[SW] Gagal download: ${url}`, error);
        }
      }
    })
  );
});

// --- 2. EVENT FETCH: Strategi Cerdas ---
self.addEventListener('fetch', event => {
  // A. JIKA REQUEST ADALAH HALAMAN HTML (Navigasi antar page)
  // Strategi "Network First": Coba internet dulu biar dapat data terbaru (database), 
  // kalau gagal/offline baru ambil versi terakhir yang tersimpan di cache.
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then((networkResponse) => {
          // Sukses ambil dari internet -> Simpan kopi terbarunya ke cache untuk nanti
          return caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, networkResponse.clone());
            return networkResponse;
          });
        })
        .catch(() => {
          // Gagal (Offline) -> Ambil versi terakhir dari cache
          console.log('[SW] Offline mode: Serving cached page');
          return caches.match(event.request);
        })
    );
    return;
  }

  // B. JIKA REQUEST ADALAH ASET STATIS (Gambar, CSS, JS, Font)
  // Strategi "Cache First": Cek cache dulu biar cepat loadingnya, kalau gak ada baru internet.
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse; // Ada di cache, langsung pakai
      }
      // Gak ada di cache, ambil dari internet
      return fetch(event.request);
    })
  );
});

// --- 3. EVENT ACTIVATE: Bersihkan Cache Lama ---
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(cacheName => {
          // Hapus cache yang namanya beda dengan versi sekarang (v3)
          return cacheName.startsWith('laravel-pwa-') && cacheName !== CACHE_NAME;
        }).map(cacheName => {
          console.log('[SW] Menghapus cache lama:', cacheName);
          return caches.delete(cacheName);
        })
      );
    })
  );
  self.clients.claim(); // SW langsung mengontrol halaman tanpa perlu reload
});