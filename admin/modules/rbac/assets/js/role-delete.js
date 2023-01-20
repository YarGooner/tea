function removeRole(tab, id) {
    var confirm = window.confirm('Вы действительно хотите удалить роль '+id+'?');
    if(confirm === true) {
        var sendData = encodeURI('id=' + id + '&tab=' + tab);
        var data = sendRoleAjax('rbac/default/delete-role', sendData, tab);
            if (tab == 'model'){
                var controllerData = sendRoleAjax('rbac/default/delete-role','tab=controller','controller');
            }
            if (tab == 'controller'){
                var modelData = sendRoleAjax('rbac/default/delete-role','tab=model','model');
        }
    }
}



function sendRoleAjax(url, sendData, tab){
    var request = new XMLHttpRequest();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(sendData);
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                var data = request.responseText;
                refreshTab(data,tab);
            } catch (err) {
                console.log(err.message + " in " + request.responseText);
            }
        }
    };
}

function refreshTab(data,tab) {
    var refreshDiv = document.getElementById(tab + '-permission');
    refreshDiv.innerHTML = data;
}