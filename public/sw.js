// Nama cache (versi)
const CACHE_NAME = 'laravel-pwa-v1';

// File-file yang ingin disimpan di cache (INI DAFTAR YANG BARU)
const urlsToCache = [
  '/',
  '/manifest.json', // File manifest itu sendiri
  '/favicon.ico',   // Ikon di tab browser
  
  // Pastikan path ke ikon PWA kamu benar (sesuai manifest.json):
  '/images/icons/icon-192.png', 
  '/images/icons/icon-512.png'
  
];

// 1. Saat PWA di-install (pertama kali dibuka)
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache dibuka, menambahkan file...');
        // 'addAll' akan gagal jika SALAH SATU file di atas 404
        return cache.addAll(urlsToCache); 
      })
      .catch(err => {
        // Jika masih gagal, ini akan memberi tahu kita file mana yg salah
        console.error('Cache addAll GAGAL:', err); 
      })
  );
});

// 2. Saat browser mengambil (fetch) file apa pun
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response; // Ambil dari cache
        }
        return fetch(event.request); // Ambil dari internet
      })
  );
});

// 3. (Opsional tapi bagus) Hapus cache lama jika ada versi baru
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(cacheName => {
          // Hapus cache apa pun yang BUKAN cache kita saat ini
          return cacheName.startsWith('laravel-pwa-') && cacheName !== CACHE_NAME;
        }).map(cacheName => {
          return caches.delete(cacheName);
        })
      );
    })
  );
});