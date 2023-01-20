function refreshInfo(url) {
    var request = new XMLHttpRequest();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                var data = request.responseText;
                if(data == 'true'){
                    console.log();
                }
            } catch (err) {
                console.log(err.message + " in " + request.responseText);
            }
        }
    }
}