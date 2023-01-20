//Показ формы добавления
function showForm() {
    var winnerForm = document.getElementById('winner-form');
    if (winnerForm.style.display == 'none') {
        winnerForm.style.display = '';
    } else winnerForm.style.display = 'none';
    return false;
}


//Добавление записи
function winnerRefresh() {
    var form = document.forms[0];
    var request = new XMLHttpRequest();
    request.open('POST', 'winner/index', true);
    request.setRequestHeader('accept', 'application/json');
    var formData = new FormData(form);
    request.send(formData);
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                var refresh = document.getElementById('refresh');
                var accordion = document.getElementById('accordion');
                accordion.parentNode.removeChild(accordion);
                var data = request.response;
                refresh.innerHTML = data;
            } catch (err) {
                console.log(err.message + " in " + request.responseText);
            }
        }
    }
}

//удаление записи

function winnerDelete(id){
    if(confirm('Вы действительно хотите удалить запись?')) {
        var request = new XMLHttpRequest();
        // id = {'id': id};
        request.open('POST', 'winner/delete', true);
        request.setRequestHeader('id', id)
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                try {
                    var refresh = document.getElementById('refresh');
                    var accordion = document.getElementById('accordion');
                    accordion.parentNode.removeChild(accordion);
                    var data = request.response;
                    console.log(data);
                    refresh.innerHTML = data;
                    // refresh(request.responce);
                } catch (err) {
                    console.log(err.message + " in " + request.responseText);
                }
            }
        }
    }
}

//Отправка писем победителям

var sendList = [];
//Составляем список отмеченных
function changeSendList(id, userId) {
    var checkbox = document.getElementById(id);
    if (checkbox.checked == true) {
        sendList.push(userId);
    } else for (var i = 0; i < sendList.length; i++) {
        if (sendList[i] == userId) {
            sendList.splice(i,1);
        }
    }
    addSendButton();
}
//Показываем кнопку отправки
function addSendButton() {
    if(document.getElementById('send-button') == null) {
        var table = document.getElementById('accordion');
        var sendButton = document.createElement('a');
        sendButton.setAttribute('onClick', 'sendAjax()');
        sendButton.className = 'btn btn-success';
        sendButton.id = 'send-button';
        sendButton.innerText = 'Отправить письма';
        table.appendChild(sendButton);
    }
    if(sendList.length == 0){
        var sendButton = document.getElementById('send-button');
        sendButton.parentNode.removeChild(sendButton);
    }
}
//Отправляем письма по кнопке
// function sendAjax() {
//     var request = new XMLHttpRequest();
//     request.open('POST', 'winner/send', true);
//     // request.setRequestHeader('accept', 'application/json');
//     request.send(sendList);
//     request.onreadystatechange = function () {
//         if (request.readyState == 4 && request.status == 200) {
//             try {
//                 var data = request.responseText;
//                 if(data == 'true'){
//                     createAlert('Письма отправлены!');
//                 }
//                 uncheck();
//                 sendList = [];
//                 addSendButton();
//             } catch (err) {
//                 console.log(err.message + " in " + request.responseText);
//             }
//         }
//     }
// }
//Информируем об успешной отправке
function createAlert(message){
    var alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible';
    alert.innerText = message;
    var close = document.createElement('a');
    close.className = 'close';
    close.setAttribute('data-dismiss','alert');
    close.setAttribute('aria-label','close');
    close.innerText = '×';
    var table = document.getElementById('accordion');
    table.appendChild(alert);
    alert.appendChild(close);
}

//Очищаем список
function uncheck()
{
    var uncheck=document.getElementsByTagName('input');
    for(var i=0;i<uncheck.length;i++)
    {
        if(uncheck[i].type=='checkbox')
        {
            uncheck[i].checked=false;
        }
    }
}

//формируем меню
function renderMenu(){
    var menu = document.getElementById('w2');
    var request = new XMLHttpRequest();
    request.open('GET', 'route', true);
    request.setRequestHeader('accept', 'application/json');
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var data = JSON.parse(request.response);
            var items = '';
            var groups = {};
            console.log(data);
            for (key in data){
                var i = 0;
                if(data[key]['group'] != undefined) {
                    // console.log(data[key]['group']);
                    var group = data[key]['group'];
                    if (group in groups == false) {
                        groups[group] = '<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">' + data[key]['group'] + '<span class="caret"></span></a><ul id="w4" class="dropdown-menu"><li><a href="' + data[key]['name'] + '" tabindex="-1">' + data[key]['translation'] + '</a></li>';
                        i++;
                    } else {
                        groups[group] += '<li><a href="' + data[key]['name'] + '" tabindex="-1">' + data[key]['translation'] + '</a></li>';
                        i++;
                    }

                } else items += '<li><a href=\''+data[key]['name']+'\'>'+data[key]['translation']+'</li>';


                // console.log(data[key]['name']);
            }
            var count = 0;
            for (key in groups) {
                groups[key] += '</ul></li>';
                items += groups[key];
                // count++;
            }
            console.log(items);
            menu.innerHTML = items;
        }
    }
}

// renderMenu();