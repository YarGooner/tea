function refreshModelPermission(role_id,field_id,type){
    var checkbox = document.getElementById('checkbox_' + role_id + '_' + field_id + '_' + type);
    if(checkbox.checked === true){
        var url = 'rbac/role-model-permission/add';
    } else {
        var url = 'rbac/role-model-permission/remove';
    }
    sendModelPermissionAjax(url, role_id, field_id, type);
}

function sendModelPermissionAjax(url,role_id,field_id, type) {
    var request = new XMLHttpRequest();
    request.open('POST', url, true);
    // request.setRequestHeader('accept', 'application/json');
    var sendData = encodeURI('role_id=' + role_id + '&field_id=' + field_id + '&type=' + type);
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
    }
}