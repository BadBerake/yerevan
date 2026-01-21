const CACHE_NAME = 'yerevango-v2';
const RUNTIME_CACHE = 'yerevango-runtime';

// Assets to cache on install
const PRECACHE_ASSETS = [
    '/',
    '/css/style.css',
    '/css/yerevango-maps.css',
    '/css/components/search.css',
    '/css/components/reviews.css',
    '/js/yerevango-maps.js',
    '/js/search.js',
    '/js/pwa.js',
    '/assets/images/icon-192.png',
    '/assets/images/icon-512.png',
    '/offline.html'
];

// Install event - precache assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');

    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Precaching assets');
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests
    if (url.origin !== location.origin) {
        return;
    }

    // Handle API requests differently (network first)
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirst(request));
        return;
    }

    // For navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }

    // For other requests (cache first)
    event.respondWith(cacheFirst(request));
});

/**
 * Cache First Strategy
 * Try cache, fallback to network, update cache
 */
async function cacheFirst(request) {
    const cache = await caches.open(RUNTIME_CACHE);
    const cachedResponse = await cache.match(request);

    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);

        if (networkResponse && networkResponse.status === 200) {
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.log('[Service Worker] Fetch failed:', error);

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }

        throw error;
    }
}

/**
 * Network First Strategy
 * Try network, fallback to cache
 */
async function networkFirst(request) {
    const cache = await caches.open(RUNTIME_CACHE);

    try {
        const networkResponse = await fetch(request);

        if (networkResponse && networkResponse.status === 200) {
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.log('[Service Worker] Network failed, trying cache:', error);

        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }

        throw error;
    }
}

// Background Sync (when connection is restored)
self.addEventListener('sync', (event) => {
    console.log('[Service Worker] Background sync:', event.tag);

    if (event.tag === 'sync-reviews') {
        event.waitUntil(syncReviews());
    }
});

async function syncReviews() {
    // Placeholder for syncing pending reviews
    console.log('[Service Worker] Syncing reviews...');
}

// Push Notifications
self.addEventListener('push', (event) => {
    console.log('[Service Worker] Push received');

    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Yerevango';
    const options = {
        body: data.body || 'New notification',
        icon: '/assets/images/logo-icon.png',
        badge: '/assets/images/logo-icon.png',
        vibrate: [200, 100, 200],
        data: data.url || '/',
        actions: data.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[Service Worker] Notification click');

    event.notification.close();

    const urlToOpen = event.notification.data || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Check if already open
            for (const client of clientList) {
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }

            // Open new window
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Message handling
self.addEventListener('message', (event) => {
    console.log('[Service Worker] Message received:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            caches.open(RUNTIME_CACHE).then((cache) => {
                return cache.addAll(event.data.urls);
            })
        );
    }
});
