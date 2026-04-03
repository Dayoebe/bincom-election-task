const CACHE_NAME = 'bincom-election-dashboard-v1';
const APP_SHELL = [
    '/polling-unit-results',
    '/lga-result-summary',
    '/polling-unit-results/create',
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/apple-touch-icon.png',
    '/icons/favicon-32x32.png',
    '/Oyetoke_Adedayo_E.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin || url.pathname.startsWith('/livewire')) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith((async () => {
            try {
                const response = await fetch(request);
                const cache = await caches.open(CACHE_NAME);

                cache.put(request, response.clone());

                return response;
            } catch {
                return (await caches.match(request)) || (await caches.match('/polling-unit-results'));
            }
        })());

        return;
    }

    event.respondWith((async () => {
        const cached = await caches.match(request);

        if (cached) {
            return cached;
        }

        const response = await fetch(request);

        if (
            response.ok &&
            (
                request.destination === 'style' ||
                request.destination === 'script' ||
                request.destination === 'image' ||
                url.pathname.startsWith('/build/') ||
                url.pathname.startsWith('/icons/')
            )
        ) {
            const cache = await caches.open(CACHE_NAME);

            cache.put(request, response.clone());
        }

        return response;
    })());
});
