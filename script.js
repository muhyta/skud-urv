function fill_change(id) {
    var w=document.getElementById('change');
    var inputs= w.getElementsByTagName('input');
}

function start() {
	document.getElementById('wait_block').style.visibility = "visible";
	document.getElementById('wait').style.visibility = "visible";
}

function subf(fm){
	var hr=document.getElementById(fm);
	fm.submit();
}

function next(t,d,c) {
    if (c==6) c1=1; else c1=c+1;
    for (i=1;i<7;i++) {
        document.getElementById(d+'_'+i).style.backgroundColor='';
    }
    if (c!=0) {
        var t2 = document.getElementById(t.id.substr(0,10)+'_'+(c1));
        t2.style.backgroundColor = '#5c5';
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open("GET", "index.php?in=1&c="+c+"&date="+d, true);
        xmlHttp.send(null);
    }
    else {
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open("GET", "index.php?in=1&c="+c+"&date="+d, true);
        xmlHttp.send(null);
    }
}

function parsSKUDjs() {
    var tr = document.getElementsByClassName('tab_cadrehov')[0].getElementsByTagName('tr');
    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td');
        var date=td[0].innerHTML;
        var fio=td[1].innerHTML;
        var post=td[2].innerHTML;
        var dep=td[3].innerHTML;
        var time=td[6].innerHTML;
        var mtime=td[7].innerHTML;
        var begin=td[4].innerHTML;
        var end=td[5].innerHTML;
        var xmlHttp = new XMLHttpRequest();
        var s = td[8];
        xmlHttp.onreadystatechange=function(s){
            if (xmlHttp.readyState != 4) return
            clearTimeout(timeout)
            if (xmlHttp.status == 200) {
                s.innerHTML = xmlHttp.responseText;
            } else {
                handleError(xmlHttp.statusText)
            }
        }
        xmlHttp.open("POST", "input.php", true);
        xmlHttp.send("fio="+fio+"&date="+date+"&time="+time+"&mtime="+mtime+"&begin="+begin+"&end="+end+"&post="+post+"&dep="+dep);
        function handleError(message, s) {s.innerHTML = xmlHttp.responseText + message;}
        var timeout = setTimeout( function(s){xmlHttp.abort(); handleError("Time over",s)}, 10000);
    }
}

function telNumjs(num,i) {
    var url = null;
    var xmlHttp = null;
    xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = ProcessRequest;
    xmlHttp.withCredentials = true;
    function ProcessRequest() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
            var s=xmlHttp.responseText;
            if (s != '0') {
                id.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                num_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                user_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                organization_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                comment_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                if (s) black_new.checked = true;
                else black_new.checked = false;
            }
            else
                switch (i) {
                case 2:
                    alert("Изменено");
                    break;
                case 3:
                    alert("Удалено");
                    break;
                case 4:
                    alert("Добавлено");
                    break;
                default: break;
            }
        }
    }

    switch (i) {
        case 1: //get
            url = "ajax.php?id="+num+"&i="+i;
            break;
        case 2: //edit
            url = "ajax.php?id="+num_new.value+"&num_new="+num_new.value+"&user_new="+user_new.value+"&black_new="+black_new.value+"&organization_new="+organization_new.value+"&comment_new="+comment_new.value+"&i="+i;
            break;
        case 3: //del
            url = "ajax.php?id="+num+"&i="+i;
            break;
        case 4: //add
            url = "ajax.php?id="+num_new.value+"&num_new="+num_new.value+"&user_new="+user_new.value+"&black_new="+black_new.value+"&organization_new="+organization_new.value+"&comment_new="+comment_new.value+"&i="+i;
            break;
        default: break;
    }
    xmlHttp.open("GET", url, false);
    xmlHttp.send();
}