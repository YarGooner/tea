function changeRole(user_id,role_id) {
    var request = new XMLHttpRequest();
    request.open('POST', 'rbac/default/change-assign', true);
    // request.setRequestHeader('accept', 'application/json');
    var sendData = encodeURI('role_id=' + role_id + '&user_id=' + user_id);
    // console.log(sendData);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(sendData);
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                var data = request.responseText;
                    renewUsers(data);
            } catch (err) {
                console.log(err.message + " in " + request.responseText);
            }
        }
    };
}

function renewUsers(data){
    var renewDiv = document.getElementById('user-ajax');
        renewDiv.innerHTML = data;
    console.log(renewDiv);
}