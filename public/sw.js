self.addEventListener('push', function(event) {
    const data = event.data.json();
    self.registration.showNotification(data.title, {
        body: data.body,
        icon: './images/favicon.png',  // Ruta del icono de la notificación
        vibrate: [200, 100, 200],  // Patrón de vibración
    });
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('http://localhost/portal-autorizaciones/public/pending')
    );
});