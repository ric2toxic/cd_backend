self.addEventListener("message", function(e) {
    var args = e.data.args;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', args);
    xhr.onload = function() {
        if (xhr.status === 200) {
            postMessage(xhr.responseText);
        }
        else {
            postMessage('No Record Found');
        }
    };
    xhr.send();

}, false);

