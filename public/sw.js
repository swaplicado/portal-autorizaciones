self.addEventListener('push', function(event) {
    try {
        console.log('Push recibido:', event);

        let notificationData;
        if (event.data) {
            try {
                notificationData = event.data.json();
            } catch (error) {
                console.error('Error al parsear los datos:', error);
                notificationData = { title: 'Notificación', body: 'Tienes un nuevo mensaje' };
            }
        } else {
            notificationData = { title: 'Notificación', body: 'Tienes un nuevo mensaje' };
        }

        const title = notificationData.title || 'Notificación';
        const options = {
            body: notificationData.body || 'Tienes un nuevo mensaje',
            icon: notificationData.icon || './images/aeth_mini.png',
            badge: notificationData.badge || './images/aeth_mini.png',
            data: notificationData.data || {},
        };

        event.waitUntil(
            self.registration.showNotification(title, options)
                .then(() => console.log("Notificación mostrada"))
                .catch(error => console.error("Error mostrando la notificación:", error))
        );
    } catch (error) {
        console.error('Error en el evento push:', error);
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    let url = '/'; // URL por defecto
    if (event.notification.data && event.notification.data.url) {
        url = event.notification.data.url; // Usar la URL proporcionada en los datos
    }

    event.waitUntil(
        clients.openWindow(url)
    );
});