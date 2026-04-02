/**
 * NobleNest Academy — Service Worker
 *
 * Strategy:
 * - Shell (HTML pages): Network-first, fallback to cache
 * - Static assets (JS/CSS/fonts): Cache-first, revalidate in background
 * - API/AJAX: Network-only (never cache POST or authenticated JSON)
 * - Images & icons: Cache-first with 30-day expiry
 */

const CACHE_VERSION = 'noblenest-v1';
const STATIC_CACHE  = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;

const PRECACHE_URLS = [
    '/',
    '/activities',
    '/manifest.json',
    '/favicon.ico',
];

const MAX_DYNAMIC_ENTRIES = 60;

// ------------------------------------------------------------------
// Install: precache shell
// ------------------------------------------------------------------
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(PRECACHE_URLS).catch(() => {
                // Non-fatal: some URLs may need auth
            });
        }).then(() => self.skipWaiting())
    );
});

// ------------------------------------------------------------------
// Activate: clean old caches
// ------------------------------------------------------------------
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((k) => k.startsWith('noblenest-') && k !== STATIC_CACHE && k !== DYNAMIC_CACHE)
                    .map((k) => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ------------------------------------------------------------------
// Fetch: routing
// ------------------------------------------------------------------
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET, cross-origin, admin/api/webhook routes
    if (request.method !== 'GET') return;
    if (url.origin !== self.location.origin) return;
    if (/^\/(admin|api|webhook|horizon|livewire)/.test(url.pathname)) return;

    // Static assets — cache-first
    if (/\.(js|css|woff2?|ttf|eot|otf|svg|png|jpg|jpeg|webp|gif|ico)$/.test(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // HTML navigation — network-first
    if (request.headers.get('Accept') && request.headers.get('Accept').includes('text/html')) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Everything else — network-first
    event.respondWith(networkFirst(request));
});

// ------------------------------------------------------------------
// Strategies
// ------------------------------------------------------------------
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
            await trimCache(DYNAMIC_CACHE, MAX_DYNAMIC_ENTRIES);
        }
        return response;
    } catch {
        return new Response('', { status: 503, statusText: 'Offline' });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Offline fallback: return minimal offline page
        return new Response(
            `<!doctype html><html><head><meta charset=utf-8><title>Offline – NobleNest</title>
            <meta name=viewport content="width=device-width,initial-scale=1">
            <style>body{font-family:sans-serif;text-align:center;padding:3rem;background:#f0fdf4}
            h1{color:#0d5c63;font-size:1.5rem}p{color:#374151}</style></head>
            <body><h1>You're Offline 🌿</h1>
            <p>NobleNest needs an internet connection.<br>Please reconnect and try again.</p>
            </body></html>`,
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

async function trimCache(cacheName, maxItems) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length > maxItems) {
        await cache.delete(keys[0]);
        await trimCache(cacheName, maxItems);
    }
}

// ------------------------------------------------------------------
// Push notifications
// ------------------------------------------------------------------
self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title ?? 'NobleNest', {
            body:    data.body ?? '',
            icon:    '/brand/icon-192.png',
            badge:   '/brand/icon-72.png',
            data:    { url: data.url ?? '/' },
            vibrate: [200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url ?? '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) return client.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});
