// Service Worker for Akreditasi Cerdas PWA
const CACHE_NAME = 'akreditasi-cerdas-v1.0.0';
const OFFLINE_URL = '/offline';

// Resources to cache immediately
const STATIC_CACHE_URLS = [
    '/',
    '/manifest.json',
    OFFLINE_URL
];

// Dynamic resources to cache when accessed
const DYNAMIC_CACHE_PATTERNS = [
    /^\/api\//,  // API calls
    /\.(?:png|jpg|jpeg|svg|gif|webp)$/i,  // Images
    /\/documents\/\d+$/,  // Document pages
    /\/templates\/\d+$/   // Template pages
];

// Install event - cache static resources
self.addEventListener('install', (event) => {
    console.log('[ServiceWorker] Install');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[ServiceWorker] Caching static resources');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                return self.skipWaiting();
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[ServiceWorker] Activate');

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[ServiceWorker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip chrome-extension requests
    if (event.request.url.startsWith('chrome-extension://')) {
        return;
    }

    // Skip Livewire requests
    if (event.request.url.includes('/livewire/')) {
        return;
    }

    // Handle API requests with network-first strategy
    if (event.request.url.includes('/api/')) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Cache successful API responses
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Return cached version if network fails
                    return caches.match(event.request)
                        .then((cachedResponse) => {
                            if (cachedResponse) {
                                return cachedResponse;
                            }
                            // Return offline page for failed API calls
                            return caches.match(OFFLINE_URL).then(offlineResponse => {
                                return offlineResponse || new Response('Network error occurred', {
                                    status: 503,
                                    statusText: 'Service Unavailable',
                                    headers: new Headers({ 'Content-Type': 'text/plain' })
                                });
                            });
                        });
                })
        );
        return;
    }

    // Handle static resources and pages with cache-first strategy
    event.respondWith(
        caches.match(event.request)
            .then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                return fetch(event.request)
                    .then((response) => {
                        // Don't cache non-successful responses
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Cache dynamic resources that match patterns
                        const shouldCache = DYNAMIC_CACHE_PATTERNS.some(pattern =>
                            pattern.test(event.request.url)
                        );

                        if (shouldCache) {
                            const responseToCache = response.clone();
                            caches.open(CACHE_NAME)
                                .then((cache) => {
                                    cache.put(event.request, responseToCache);
                                });
                        }

                        return response;
                    })
                    .catch(() => {
                        // Return offline page for navigation requests
                        if (event.request.mode === 'navigate') {
                            return caches.match(OFFLINE_URL).then(offlineResponse => {
                                return offlineResponse || new Response('Offline mode: Page not available', {
                                    status: 200,
                                    headers: new Headers({ 'Content-Type': 'text/html' })
                                });
                            });
                        }
                        
                        // Default fallback for other resources
                        return new Response('Resource unavailable offline', {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: new Headers({ 'Content-Type': 'text/plain' })
                        });
                    });
            })
    );
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    console.log('[ServiceWorker] Background sync:', event.tag);

    if (event.tag === 'background-sync-documents') {
        event.waitUntil(syncOfflineDocuments());
    }

    if (event.tag === 'background-sync-comments') {
        event.waitUntil(syncOfflineComments());
    }
});

// Push notifications
self.addEventListener('push', (event) => {
    console.log('[ServiceWorker] Push received:', event);

    let data = {};
    if (event.data) {
        data = event.data.json();
    }

    const options = {
        body: data.body || 'Ada pembaruan di Akreditasi Cerdas',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Lihat',
                icon: '/images/icons/icon-192x192.png'
            },
            {
                action: 'close',
                title: 'Tutup'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(
            data.title || 'Akreditasi Cerdas',
            options
        )
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('[ServiceWorker] Notification click:', event);

    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url || '/dashboard')
        );
    }
});

// Offline data sync functions
async function syncOfflineDocuments() {
    try {
        // Get offline documents from IndexedDB/localStorage
        const offlineDocuments = await getOfflineDocuments();

        for (const doc of offlineDocuments) {
            try {
                await syncDocumentToServer(doc);
                await removeOfflineDocument(doc.id);
            } catch (error) {
                console.error('Failed to sync document:', doc.id, error);
            }
        }
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}

async function syncOfflineComments() {
    try {
        const offlineComments = await getOfflineComments();

        for (const comment of offlineComments) {
            try {
                await syncCommentToServer(comment);
                await removeOfflineComment(comment.id);
            } catch (error) {
                console.error('Failed to sync comment:', comment.id, error);
            }
        }
    } catch (error) {
        console.error('Comment sync failed:', error);
    }
}

// Placeholder functions for offline data management
// These would be implemented with IndexedDB in a real application
function getOfflineDocuments() {
    return Promise.resolve([]);
}

function getOfflineComments() {
    return Promise.resolve([]);
}

function syncDocumentToServer(doc) {
    return Promise.resolve();
}

function syncCommentToServer(comment) {
    return Promise.resolve();
}

function removeOfflineDocument(id) {
    return Promise.resolve();
}

function removeOfflineComment(id) {
    return Promise.resolve();
}