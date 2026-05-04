importScripts('js/firebase-app.js');
importScripts('js/firebase-messaging.js');


//$.getScript("https://www.gstatic.com/firebasejs/3.5.2/firebase-app.js", function () {
//
//}).then(function () {
//    $.getScript("https://www.gstatic.com/firebasejs/3.5.2/firebase-messaging.js", function () {
//
//    });
//}).then(function () {

    firebase.initializeApp({
        'messagingSenderId': '1017269354509'
    });
//
    var messaging = firebase.messaging();
// Installs service worker
    self.addEventListener('install', function (event) {
        console.log('Service worker installed');
    });

    self.addEventListener('notificationclick', function (event) {
            console.log('Event',event.notification.data);
        // Event actions derived from event.notification.data from data received
        var eventURL = event.notification.data;
        event.notification.close();
        if (event.action === 'view') {
            clients.openWindow(eventURL.confirm);
        } else {
            clients.openWindow(eventURL.decline);
        }
    }, false);

    messaging.setBackgroundMessageHandler(function (payload) {
        // Parses data received and sets accordingly
        var data = JSON.parse(payload.data.notification);
        var notificationTitle = data.title;
        var notificationOptions = {
            body: data.body,
            icon: '/'+data.icon,
//           icon: 'https://www.wholesalebox.in/image/catalog/rsz_wsb_tmp_logo_286.png',
//            actions: [
//                {action: 'view', title: 'View'}
//                ,
//                {action: 'cancel', title: 'Cancel'}
//            ],
            // For additional data to be sent to event listeners, needs to be set in this data {}
            data: {confirm: data.click_action, decline: data.click_action}
        };
        
        return self.registration.showNotification(notificationTitle, notificationOptions);
    });

//});

// TODO: fill in messaging sender id