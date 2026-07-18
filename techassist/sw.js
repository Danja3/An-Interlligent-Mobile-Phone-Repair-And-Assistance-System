// TechAssist service worker — offline shell + fast repeat asset loads (vanilla).
const VERSION = 'v1';
const CACHE = 'techassist-' + VERSION;
const PRECACHE = ['./offline.html', './manifest.webmanifest', './assets/css/styles.css', './assets/js/app.js', './assets/img/icon.svg'];

self.addEventListener('install', (e) => {
  e.waitUntil(caches.open(CACHE).then((c) => c.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const req = e.request;
  if (req.method !== 'GET') return;
  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;

  // Page navigations: network-first (PHP is dynamic), offline page as fallback.
  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).catch(() => caches.match('./offline.html')));
    return;
  }

  // Static assets: cache-first.
  if (/\.(?:css|js|svg|png|jpe?g|webp|ico|woff2?)$/.test(url.pathname)) {
    e.respondWith(
      caches.match(req).then((hit) => hit || fetch(req).then((res) => {
        const copy = res.clone();
        caches.open(CACHE).then((c) => c.put(req, copy));
        return res;
      }).catch(() => hit))
    );
  }
});
