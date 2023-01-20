function refreshPermission(role_id,action_id){
    var checkbox = document.getElementById('checkbox_' + role_id + '_' + action_id);
    if(checkbox.checked === true){
        var url = 'rbac/role-permission/add';
    } else {
        var url = 'rbac/role-permission/remove';
    }
    sendPermissionAjax(url, role_id, action_id);
}

function sendPermissionAjax(url,role_id,action_id) {
    var request = new XMLHttpRequest();
    request.open('POST', url, true);
    // request.setRequestHeader('accept', 'application/json');
    var sendData = encodeURI('role_id=' + role_id + '&action_id=' + action_id);
    // console.log(sendData);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(sendData);
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
    };
}