self.addEventListener("message", function(e) {
   var args = e.data.url;
   var catID = e.data.catId;
   var xhr = new XMLHttpRequest();
   xhr.open('GET', args);
   xhr.onload = function() {
       if (xhr.status === 200) {
           postMessage({result:xhr.responseText,catId:catID});
       }
       else {
           postMessage('No Record Found');
       }
   };
   xhr.send();
}, false);