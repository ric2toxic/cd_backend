
$.getScript("js/firebase.js", function () {

}).then(function () {
    $.getScript("js/firebase-messaging.js", function () {

    });
}).then(function () {

//    <script src="https://www.gstatic.com/firebasejs/4.1.3/firebase.js"></script>
//<script>
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyCzKLjoHZRhmVJ1fV2NufZgmjP07r6XRYI",
    authDomain: "wholesale-project.firebaseapp.com",
    databaseURL: "https://wholesale-project.firebaseio.com",
    projectId: "wholesale-project",
    storageBucket: "",
    messagingSenderId: "1017269354509"
  };
  firebase.initializeApp(config);
//</script>


//      firebase.initializeApp(config);
      var messaging = firebase.messaging();

    if ('serviceWorker' in navigator) {
        
//        navigator.serviceWorker.register('js/firebase-messaging-sw.js')
navigator.serviceWorker.register('firebase-messaging-sw.js',{scope:'./'})
                .then(function (registration) {
                    // Successfully registers service worker
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
//                     registration.update();
                    messaging.useServiceWorker(registration);
                }).then(function () {
                  
            // Requests user browser permission
            return messaging.requestPermission();
        }).then(function () {
         
            //console.log(messaging.getToken());
            // Gets token
            // return messaging.getToken();
            return messaging.getToken();
          
        }).then(function (token) {
            console.log(token);
            
            // Simple ajax call to send user token to server for saving
           var url_token = $.urlParam('token');
            $.ajax({
                type: 'POST',
                url: 'index.php?route=common/createtoken&token='+url_token,
                dataType: 'json',
                data: JSON.stringify({browse_token: token}),
                contentType: 'application/json',
                success: function (data) {
                    
                    console.log('Success ', data);
                },
                error: function (err) {
                    
                    console.log('Error ', err);
                }
            })
        }).catch(function (err) {
           
            console.log('ServiceWorker registration failed: ', err);
        });
    }
});

