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
    if (i == 4) {
        id.value="";
        num_new.value="Номер";
        user_new.value="Пользователь";
        organization_new.value="Организация";
        comment_new.value="Комментарии";
        black_new.checked = false;
        return 0;
    }
    var url = null;
    var xmlHttp = null;
    xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = ProcessRequest;
    xmlHttp.withCredentials = true;
    function ProcessRequest() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
            var s=xmlHttp.responseText;
            if (s.indexOf("|") > 0) {
                id.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                num_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                user_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                organization_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                comment_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                if (s==1) black_new.checked = true;
                else black_new.checked = false;
            }
            else {
                if (s=="Добавлено") {
                    var t=main_table.insertRow(1);
                    var user="";
                    for (var i=0;i<users.options.length;i++)
                        if (users.options[i].value == user_new.value) {user=users.options[i].innerText;break;}
                    t.innerHTML="<td>new</td><td>"+num_new.value+"</td><td>"+user+"</td><td>"+(black_new.checked?"Личный":"Рабочий")+"</td><td>"+organization_new.value+"</td><td>"+comment_new.value+"</td>";
                    t.className="tab_bg_1";
                }
                alert(s);
            }
        }
    }

    switch (i) {
        case 1: //get
            url = "ajax.php?id="+num+"&i="+i;
            break;
        case 2: //edit
            url = "ajax.php?id="+id.value+"&num_new="+num_new.value+"&user_new="+user_new.value+"&black_new="+((black_new.checked)?1:0)+"&organization_new="+organization_new.value+"&comment_new="+comment_new.value+"&i="+i;
            break;
        case 3: //del
            url = "ajax.php?id="+num+"&i="+i;
            break;
        default: break;
    }
    xmlHttp.open("GET", url, false);
    xmlHttp.send();
}

var glob_th="";

function usrTIMEjs(id,i,domain,th) {
    if (i < 1 || i > 8) {
        alert("Некоректный код операции "+i);
        return 0;
    }
    if (i == 8) {
        id_time.value=0;
        fio.value="";
        date.value="";
        in_time.value="";
        out_time.value="";
        time.value="";
        m_time.value="";
        return 0;
    }
    domain = typeof domain !== 'undefined' ? domain : "";
    th = typeof th !== 'undefined' ? th : document.getElementsByName('table');
    if (i == 4) {
        id_user.value=0;
        w_f_new.value="";
        w_n_new.value="";
        w_p_new.value="";
        w_l_new.value="";
        w_post_new.value="";
        w_otdel_new.value="";
        w_i_new.value="";
        bidate.value="";
        w_fired.checked = false;
        foto.src = domain+"/photos/none.png";
        return 0;
    }
    var url = null;
    var xmlHttp = null;
    xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = ProcessRequest;
    xmlHttp.withCredentials = true;
    function ProcessRequest() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
            var s=xmlHttp.responseText;
            if (s.indexOf("|") > 0) {
                if (i == 1) {
                    /*
                     id
                     w_f_new
                     w_n_new
                     w_p_new
                     w_l_new
                     w_post_new
                     w_otdel_new
                     w_fired
                     w_i_new
                    */
                    id_user.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_f_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_n_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_p_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_l_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_post_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_otdel_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    var n=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    w_i_new.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    bidate.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    if (n == 1) w_fired.checked = true;
                    else w_fired.checked = false;
                    foto.src = "http://www."+domain+"/photos/"+w_l_new.value+".jpg";
                    wait_user.style.visibility="visible";
                    glob_th = glob_th != '' ? glob_th : th;
                    glob_th.style.backgroundColor = '#f2f2f2';
                    glob_th=th;
                    th.style.backgroundColor = '#8f8';
                }
                if (i == 5) {
                    /*
                     id
                     tURVData.IN_WORK_DATE,
                     Workers.F_Worker,
                     Workers.I_Worker,
                     tURVData.DAY_START,
                     tURVData.DAY_END,
                     tURVData.IN_WORK_TIME_MINUTES,
                     tURVData.MORNING_TIME_MINUTES
                     */
                    id_time.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    date.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    fio.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    in_time.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    out_time.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    time.value=s.substr(0,s.indexOf('|')); s=s.substr(s.indexOf('|')+1);
                    m_time.value=s;
                    wait_time.style.visibility="visible";
                }
            }
            else alert(s);
        }
    }

    switch (i) {
        case 1: //get
            url = "ajax.php?id="+id+"&i="+i;
            break;
        case 2: //edit
            url = "ajax.php?id="+id_user.value+"&f_new="+w_f_new.value+"&n_new="+w_n_new.value+"&p_new="+w_p_new.value+"&l_new="+w_l_new.value+"&post_new="+w_post_new.value+"&otdel_new="+w_otdel_new.value+"&fired="+((w_fired.checked)?1:0)+"&i_new="+w_n_new.value.substr(0,1)+"."+w_p_new.value.substr(0,1)+"."+"&i="+i;
            break;
        case 3: //del
            url = "ajax.php?id="+id+"&i="+i;
            break;
        case 5: //get
            url = "ajax.php?id="+id+"&i="+i;
            break;
        case 6: //edit
            url = "ajax.php?id="+id_time.value+"&date="+date.value+"&in="+in_time.value+"&out="+out_time.value+"&in_time="+time.value+"&m_time="+m_time.value+"&i="+i;
            break;
        case 7: //del
            url = "ajax.php?id="+id+"&i="+i;
            break;
        default: break;
    }
    xmlHttp.open("GET", url, false);
    xmlHttp.send();
}
