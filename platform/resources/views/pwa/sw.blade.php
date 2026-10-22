self.addEventListener('push', (event) => {
    const payload = event.data ? event.data.json() : {};

    event.waitUntil(
        self.registration.showNotification(payload.title || 'SHOPLINK Intelligence', {
            body: payload.body || '',
            icon: payload.icon || '/icons/shoplink-icon-192.svg',
            badge: payload.badge || '/icons/shoplink-badge.svg',
            tag: payload.tag || 'shoplink-general',
            data: payload.data || { url: '/dashboard' },
        }),
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (const client of windowClients) {
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        }),
    );
});
