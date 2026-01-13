// Service Worker for Push Notifications
self.addEventListener('push', function(event) {
    if (!event.data) {
        return;
    }

    const data = event.data.json();
    
    const options = {
        body: data.body || 'You have a new notification',
        icon: data.icon || '/images/logo.png',
        badge: data.badge || '/images/badge.png',
        vibrate: [100, 50, 100],
        data: data.data || {},
        requireInteraction: data.requireInteraction || false,
        actions: [
            { action: 'open', title: 'Open' },
            { action: 'dismiss', title: 'Dismiss' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'ROSHS Notification', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    // Open the app or focus if already open
    const urlToOpen = event.notification.data.url || '/teacher/timetable';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Check if there's already a window/tab open
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(urlToOpen);
                    return client.focus();
                }
            }
            // Open new window if none found
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

self.addEventListener('pushsubscriptionchange', function(event) {
    event.waitUntil(
        self.registration.pushManager.subscribe({ userVisibleOnly: true })
            .then(function(subscription) {
                // Send the new subscription to the server
                return fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(subscription)
                });
            })
    );
});
