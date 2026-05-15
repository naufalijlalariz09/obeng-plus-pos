const CACHE_NAME = 'obeng-plus-v1';
const ASSETS_TO_CACHE = [
  '/obeng-plus-pos/pages/dashboard.php',
  '/obeng-plus-pos/manifest.json',
  // Anda bisa menambahkan file CSS/JS lokal di sini jika ada
];

// Install Service Worker & Simpan Cache
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('Opened cache');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Intercept Network Requests (Mempercepat loading)
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      // Jika ada di cache, gunakan cache. Jika tidak, ambil dari internet.
      return response || fetch(event.request);
    })
  );
});

// Update Service Worker jika ada versi baru
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});